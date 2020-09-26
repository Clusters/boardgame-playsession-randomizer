<?php
require_once("./libs/WebPageSkeleton.php");
require_once("./libs/WebPage.php");

class ShowSurveysPage extends WebPageSkeleton implements WebPage
{
    public function print_page_header()
    {
        echo $this->generate_header("List of all started surveys");
    }

    public function print_page_content()
    {
        $surveys = fetch_all_surveys();

        $link_list = "";
        $page = Page::ShowSurvey;
        foreach(array_keys($surveys) as $survey_id)
        {
            $link_list .= <<<HYPERLINK
                <a href="./index.php?page=$page&survey_id=$survey_id">$survey_id</a><br>
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