<?php

$session_started = session_start();
if(!$session_started) {
    die("Error: Session could not be started!");
}

require_once("./libs/Collection.php");
require_once("./libs/Boardgame.php");
require_once("./inc/SetAdminPage.php");
require_once("./inc/UnauthenticatedPage.php");
require_once("./inc/LoginPage.php");
require_once("./inc/LoginValidationPage.php");
require_once("./inc/NewEntryPage.php");
require_once("./inc/AdminHomePage.php");

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
                //return new NewSurveyPage();
                die("Error: Not yet implemented!");
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
        default:
            die("Error: $requested_page not implemented!");
    }
}



?>