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
    const ShowSurveys = "ShowSurveys";
    const ShowSurvey = "ShowSurvey";
    const FinishSurvey = "FinishSurvey";
    const ShowSurveyResults = "ShowSurveyResults";
    const ShowBoardgames = "ShowBoardgames";
    const ShowBoardgameDetails = "ShowBoardgameDetails";
    const Login = "Login";
    const Logout = "Logout";
    const LoginVerification = "LoginVerification";
    const NotAuthenticated = "NotAuthenticated";
    const AdminHome = "AdminHome";
    const EditBoardgame = "EditBoardgame";
    const DeleteBoardgame = "DeleteBoardgame";
    const ToggleBoardgameActivation = "ToggleBoardgameActivation";
}

abstract class Payload 
{
    const NewAdminPassword = "NewAdminPassword";
    const Login = "Login";
    const NewEntry = "NewEntry";
    const NewSurvey = "NewSurvey";
    const NewVote = "NewVote";
}

abstract class Lists
{
    /**
     * array which contains all available pages
     */
    const AllPages = array(
        Page::NewEntry => Page::NewEntry, Page::StartNewSurvey => Page::StartNewSurvey, Page::SetAdminUser => Page::SetAdminUser, Page::DeleteEntry => Page::DeleteEntry, 
        Page::ShowSurvey => Page::ShowSurvey, Page::FinishSurvey => Page::FinishSurvey, Page::ShowSurveyResults => Page::ShowSurveyResults, 
        Page::ShowBoardgameDetails => Page::ShowBoardgameDetails, Page::Login => Page::Login, Page::NotAuthenticated => Page::NotAuthenticated,
        Page::LoginVerification => Page::LoginVerification, Page::Logout => Page::Logout, Page::AdminHome => Page::AdminHome,
        Page::ShowBoardgames => Page::ShowBoardgames, Page::ShowSurveys => Page::ShowSurveys, Page::EditBoardgame => Page::EditBoardgame, Page::DeleteBoardgame => Page::DeleteBoardgame,
        Page::ToggleBoardgameActivation => Page::ToggleBoardgameActivation
    );

    const AllPayloadTypes = array(
        Payload::NewAdminPassword => Payload::NewAdminPassword, Payload::Login => Payload::Login, Payload::NewEntry => Payload::NewEntry,
        Payload::NewSurvey => Payload::NewSurvey, Payload::NewVote => Payload::NewVote
    );

    const AllTags = array(
        "anime",
        "austria",
        "bluffing",
        "campaign",
        "cardgame",
        "CCG",
        "co-op",
        "deck-building",
        "deduction",
        "detective",
        "dice",
        "dungeon-crawl",
        "economic",
        "educational",
        "english",
        "family",
        "fantasy",
        "german",
        "historic",
        "japanese",
        "kickstarter",
        "LCG",
        "micro",
        "party",
        "prehistoric",
        "present",
        "PvE",
        "PvP",
        "quiz",
        "randomizer",
        "RPG",
        "sci-fi",
        "storytelling",
        "strategy",
        "teams",
        "worker-placement "
    );
}
?>