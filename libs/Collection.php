<?php

/**
 * Enum class for different available pages.
 */
abstract class Page
{
    const NewEntry = "NewEntry";
    const StartNewSurvey = "StartNewSurvey";
    const SetAdminUser = "SetAdminUser";
    const DeleteEntry = "DeleteEntry";
    const ShowSurvey = "ShowSurvey";
    const FinishSurvey = "FinishSurvey";
    const ShowSurveyResults = "ShowSurveyResults";
    const ShowBoardgameDetails = "ShowBoardgameDetails";
    const Login = "Login";
    const Logout = "Logout";
    const LoginVerification = "LoginVerification";
    const NotAuthenticated = "NotAuthenticated";
}

/**
 * array which contains all available pages
 */
$all_pages = array(
    Page::NewEntry => Page::NewEntry, Page::StartNewSurvey => Page::StartNewSurvey, Page::SetAdminUser => Page::SetAdminUser, Page::DeleteEntry => Page::DeleteEntry, 
    Page::ShowSurvey => Page::ShowSurvey, Page::FinishSurvey => Page::FinishSurvey, Page::ShowSurveyResults => Page::ShowSurveyResults, 
    Page::ShowBoardgameDetails => Page::ShowBoardgameDetails, Page::Login => Page::Login, Page::NotAuthenticated => Page::NotAuthenticated,
    Page::LoginVerification => Page::LoginVerification, Page::Logout => Page::Logout
);

abstract class Payload 
{
    const NewAdminPassword = "NewAdminPassword";
    const Login = "Login";
}

$all_payload_types = array(
    Payload::NewAdminPassword => Payload::NewAdminPassword, Payload::Login => Payload::Login
)

?>