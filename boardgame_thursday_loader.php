<?php

$session_started = session_start();
if(!$session_started) {
    die("Error: Session could not be started!");
}

require_once("./libs/Collection.php");
require_once("./inc/SetAdminPage.php");
require_once("./inc/UnauthenticatedPage.php");
require_once("./inc/LoginPage.php");
require_once("./inc/LoginValidationPage.php");

function digest_request(string $received_payload) {

    switch ($received_payload) {
        case Payload::NewAdminPassword:
            // check if request is valid
            if((!$_SESSION["admin"] || !isset($_POST["new_password"])) && file_exists("./config.json")) 
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
            }

            // log user out
            $_SESSION["admin"] = false;
            break;
        case Payload::Login:
            if($_SESSION["admin"] || !file_exists("./config.json") || !isset($_POST["password"]))
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
            if($_SESSION["admin"] || !file_exists("./config.json")) {
                return new SetAdminPage();
            }
            return new UnauthenticatedPage();
        default:
            die("Error: $requested_page not implemented!");
    }
}



?>