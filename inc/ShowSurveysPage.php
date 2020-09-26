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
        $results_page = Page::ShowSurveyResults;
        $voting_page = Page::ShowSurvey;
        foreach($surveys as $survey_id => $survey)
        {
            if(!($survey instanceof Survey))
            {
                $type=get_class($survey);
                die("Error: Wrong item type $type received from fetch_all_surveys()!");
            }

            if($survey->active)
            {
                $voting_link = "<a href=\"./index.php?page=$voting_page&survey_id=$survey_id\">Show survey</a>";
            } else {
                $voting_link = "<span class=\"disabled-hyperlink\">Show survey</span>&nbsp;".
                "<img src=\"./resources/img/empty_hourglass.png\" width=\"20px\" height=\"20px\" title=\"Survey expired\">";
            }

            $link_list .= <<<HYPERLINK
                <p class="survey_item">
                    <label for="$survey_id">{$survey_id}:</label>&nbsp;
                    <a name="$survey_id" href="./index.php?page=$results_page&survey_id=$survey_id">Show results</a>&nbsp;|&nbsp;
                    $voting_link
                </p>
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