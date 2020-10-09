<?php

$session_started = session_start();
if(!$session_started) {
    die("Error: Session could not be started!");
}

require_once("./libs/Collection.php");
require_once("./libs/Helpers.php");
require_once("./libs/Boardgame.php");
require_once("./libs/Survey.php");
require_once("./inc/SetAdminPage.php");
require_once("./inc/UnauthenticatedPage.php");
require_once("./inc/LoginPage.php");
require_once("./inc/LoginValidationPage.php");
require_once("./inc/NewEntryPage.php");
require_once("./inc/AdminHomePage.php");
require_once("./inc/StartSurveyPage.php");
require_once("./inc/ShowSurveysPage.php");
require_once("./inc/ShowSurveyPage.php");
require_once("./inc/ShowBoardgamePage.php");
require_once("./inc/ShowSurveyResultsPage.php");
require_once("./inc/ShowBoardgamesPage.php");

function digest_request(string $received_payload) {

    switch ($received_payload) {
        case Payload::NewAdminPassword:
            // check if request is valid
            if((!isset($_SESSION["admin"]) || !$_SESSION["admin"] || !isset($_POST["new_password"])) && file_exists("./config.json")) 
            {
                break;
            }
            
            // load/create config and insert new password
            if(!file_exists("./config.json"))
            {
                $password_hash = password_hash(htmlspecialchars($_POST["new_password"]), PASSWORD_DEFAULT);
                $config = array(
                    "admin_password" => $password_hash
                );
            } else {
                $config = json_decode(file_get_contents("./config.json"), true);
                $config["admin_password"] = password_hash(htmlspecialchars($_POST["new_password"]), PASSWORD_DEFAULT);
            }

            // save new config
            $result = file_put_contents("./config.json", json_encode($config));
            if(!$result) {
                die("Error: New config could not be written. Maybe the index.php file settings are not sufficent.");
            } else {
                echo <<<SUCCESS
                    <p class="success">Password has been changed</p>
SUCCESS;
            }

            // log user out
            $_SESSION["admin"] = false;
            break;
        
        case Payload::Login:
            if((isset($_SESSION["admin"]) && $_SESSION["admin"]) || !file_exists("./config.json") || !isset($_POST["password"]))
            {
                break;
            }

            // read password hash
            $config = json_decode(file_get_contents("./config.json"), true);
            $password_hash = $config["admin_password"];

            // validate
            if (password_verify(htmlspecialchars($_POST["password"]), $password_hash))
            {
                $_SESSION["admin"] = true;
            } else {
                $_SESSION["admin"] = false;
                sleep(1); // hold program to reduce brute force attack validation speed
            }
            
            break;

        case Payload::NewEntry:
            // check if request is valid
            if(!isset($_SESSION["admin"]) || !$_SESSION["admin"]) 
            {
                break;
            }

            // read data
            if(!isset($_POST["title"]))
            {
                die("Error: The title of the game could not be read!");
            }
            $title = htmlspecialchars($_POST["title"]);

            $player_count = array();
            if(isset($_POST["player-count"])) 
            {
                // securing payload
                foreach($_POST["player-count"] as $key => $players) 
                {
                    $player_count[$key] = htmlspecialchars($players);
                }
            }

            $multisession = false;
            if(isset($_POST["multisession"])) 
            {
                $multisession = true;
            }

            $tags = array();
            if(isset($_POST["tags"])) 
            {
                // securing payload
                foreach($_POST["tags"] as $key => $tag) 
                {
                    $tags[$key] = htmlspecialchars($tag);
                }
            }

            $preview_url = "";
            if(isset($_POST["preview_url"]))
            {
                $preview_url = htmlspecialchars($_POST["preview_url"]);
            }

            $tutorial_url = "";
            if(isset($_POST["tutorial_url"]))
            {
                $tutorial_url = htmlspecialchars($_POST["tutorial_url"]);
            }

            $bgg_url = "";
            if(isset($_POST["bgg_url"]))
            {
                $bgg_url = htmlspecialchars($_POST["bgg_url"]);
            }

            // extract BGG game ID from BGG URL
            preg_match("#^https?://([^./]+.)com/[^/]+/(?<id>\d+)/#", $bgg_url, $matches);
            if(!isset($matches["id"]))
            {
                die("Error: Invalid Boardgamegeek game URL supplied!");
            }
            $bgg_id = $matches["id"];

            // save board game
            $boardgame = new Boardgame($title, $player_count, $multisession, $tags, $preview_url, $tutorial_url, $bgg_id);
            $boardgame->write_boardgame_to_json();

            break;

        case Payload::NewSurvey:
            // check if request is valid
            if(!isset($_SESSION["admin"]) || !$_SESSION["admin"]) 
            {
                break;
            }

            // read data
            if(!isset($_POST["games_amount"]))
            {
                die("Error: Missing games amount!");
            }
            $games_amount = (int)htmlspecialchars($_POST["games_amount"]);

            $until = time() + (24*5); // default 5 days
            if(isset($_POST["run_until_date"]) && $_POST["run_until_date"] != "" && isset($_POST["user_timezone"]) && $_POST["user_timezone"] != "")
            {
                //validate timezone
                $user_tz = htmlspecialchars($_POST["user_timezone"]);
                if(preg_match("#[+-]\d\d:\d\d#", $user_tz)!=1)
                {
                    die("Error: Invalid time zone format received!");
                }
                
                $until_date = htmlspecialchars($_POST["run_until_date"]);

                if(isset($_POST["run_until_time"]) && $_POST["run_until_time"] != "")
                {
                    $until_time = htmlspecialchars($_POST["run_until_time"]);
                } else {
                    $until_time = "00:00";
                }

                $until = strtotime("{$until_date}T{$until_time}:00.00{$user_tz}");
                if(!$until)
                {
                    die("Error: Invalid Date or Time format received!");
                }
            }

            $survey = new Survey();
            $survey->start_survey($games_amount, $until);

            break;
        case Payload::NewVote:
            // check if request is valid
            // todo insert some sort of check for multi-voting attempts

            if(!isset($_POST["survey_id"]))
            {
                die("Error: No survey_id found. Can not process votes for unknown survey.");
            }

            // load survey
            $survey_id = htmlspecialchars($_POST["survey_id"]);
            $surveys = fetch_all_surveys();
            if(!key_exists($survey_id, $surveys)) 
            {
                die("Error: No survey found for id '$survey_id'.");
            }
            $survey = $surveys[$survey_id];

            // load votes
            $voting = array();
            if(isset($_POST["vote"])) 
            {
                // securing payload
                foreach($_POST["vote"] as $key => $players) 
                {
                    $voting[$key] = htmlspecialchars($players);
                }
            } else {
                error_log("Warning: Blank vote received.");
                break;
            }

            $survey->digest_voting($voting);

            break;
        default:
            die("Error: Not yet implemented!");
    }
}

function page_init(string $requested_page): WebPage {
    
    switch ($requested_page) {
        case Page::Login:
            if(!file_exists("./config.json")) {
                return new SetAdminPage();
            }
            if(isset($_SESSION["admin"]) && $_SESSION["admin"]) {
                return new AdminHomePage();
            }
            return new LoginPage();
        case Page::LoginVerification:
            if(!isset($_SESSION["admin"]) || !$_SESSION["admin"])
            {
                return new LoginValidationPage(false);
            }
            return new LoginValidationPage(true);
        case Page::SetAdminUser:
            if((isset($_SESSION["admin"]) && $_SESSION["admin"]) || !file_exists("./config.json")) {
                return new SetAdminPage();
            }
            return new UnauthenticatedPage();
        case Page::NewEntry:
            if(isset($_SESSION["admin"]) && $_SESSION["admin"])
            {
                return new NewEntryPage();
            }
            return new UnauthenticatedPage();
        case Page::AdminHome:
            if(isset($_SESSION["admin"]) && $_SESSION["admin"])
            {
                return new AdminHomePage();
            }
            return new UnauthenticatedPage();
        case Page::ShowSurveys:
            if(isset($_SESSION["admin"]) && $_SESSION["admin"])
            {
                return new ShowSurveysPage();
            }
            return new UnauthenticatedPage();
        case Page::ShowSurvey:
            if(isset($_GET["survey_id"])) 
            {
                return new ShowSurveyPage(htmlspecialchars($_GET["survey_id"]));
            }
            return new LoginPage();
        case Page::ShowBoardgameDetails:
            if(isset($_GET["boardgame_id"])){
                return new ShowBoardgamePage(htmlspecialchars($_GET["boardgame_id"]));
            }
            return new LoginPage();
        case Page::ShowSurveyResults:
            if(isset($_GET["survey_id"])) 
            {
                return new ShowSurveyResultsPage(htmlspecialchars($_GET["survey_id"]));
            }
            return new LoginPage();
        case Page::StartNewSurvey:
            if(isset($_SESSION["admin"]) && $_SESSION["admin"])
            {
                return new StartSurveyPage();
            }
            return new UnauthenticatedPage();
        case Page::ShowBoardgames:
            if(isset($_SESSION["admin"]) && $_SESSION["admin"])
            {
                return new ShowBoardgamesPage();
            }
            return new UnauthenticatedPage();
        default:
            die("Error: $requested_page not implemented!");
    }
}
?>