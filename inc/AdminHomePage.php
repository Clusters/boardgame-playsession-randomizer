<?php
require_once("./libs/WebPageSkeleton.php");
require_once("./libs/WebPage.php");

class AdminHomePage extends WebPageSkeleton implements WebPage
{
    public function print_page_header()
    {
        echo $this->generate_header("Administrator Home View");
    }

    public function print_page_content()
    {
        $accessable_pages = array(
            "Show all board games" => Page::ShowBoardgames, "Enter new board game" => Page::NewEntry, "Show all surveys" => Page::ShowSurveys,
            "Start new survey" => Page::StartNewSurvey, "Change administrator password" => Page::SetAdminUser
        );

        $link_list = "";
        foreach($accessable_pages as $text => $page)
        {
            $link_list .= <<<HYPERLINK
                <a href="./index.php?page=$page">$text</a><br>
            HYPERLINK;
        }

        $content = <<<HTML
        <p>
            $link_list
        </p>
        HTML;
        echo $this->generate_body_encapsulation($content);
    }
}