<?php
require_once("./libs/WebPageSkeleton.php");
require_once("./libs/WebPage.php");

class ShowSurveyResultsPage extends WebPageSkeleton implements WebPage
{
    private $survey = null;
    private $survey_id = null;

    function __construct(int $survey_id)
    {
        $surveys = fetch_all_surveys();

        if(!key_exists($survey_id, $surveys))
        {
            die("Error: Unknown or deleted survey requested.");
        }

        $survey = $surveys[$survey_id];

        if(!($survey instanceof Survey))
        {
            $type = get_class($survey);
            die("Error: Array item was of type '$type' but expected Survey");
        }

        $this->survey = $survey;
        $this->survey_id = $survey_id;
    }

    public function print_page_header()
    {
        echo $this->generate_header("Show results of survey #$this->survey_id");
    }

    public function print_page_content()
    {
        $boardgames = fetch_all_boardgames();
        $sum_of_votes = $this->get_sum_of_votes();
        $vote_leaders = $this->get_vote_leaders();
        $page = Page::ShowBoardgameDetails;

        $survey_items = "";
        foreach($this->survey->boardgames_and_votes as $bgg_id => $votes)
        {
            if (!key_exists($bgg_id, $boardgames))
            {
                die("Error: Invalid or deleted board game found in this survey.");
            }

            $percentage = 0;
            if($votes > 0) 
            {
                $percentage = round(($votes / $sum_of_votes) * 100);
            }
            
            $leader = "";
            if(in_array($bgg_id, $vote_leaders))
            {
                $leader = ' class="leader"';
            }

            $boardgame_title = $boardgames[$bgg_id]->title;
            $multisession = "";
            if($boardgames[$bgg_id]->multisession)
            {
                $multisession="*";
            }

            $survey_items .= <<<SURVEYITEM
            <li>
                <div class="survey-result-item">
                    <progress id="$bgg_id" value="$percentage" max="100"$leader> $percentage% </progress>
                    <label for="$bgg_id"><a href="./index.php?page=$page&boardgame_id=$bgg_id">$boardgame_title</a>$multisession</label>
                </div>
            </li>
SURVEYITEM;
        }

        $content = <<<HTML
            <div id="survey-results-content">
                <br>
                <br>
                <ul>
                    $survey_items
                </ul>
            </div>
            <span id="multisession-footnote">* Multisession board game</span>
HTML;
        echo $this->generate_body_encapsulation($content);
    }

    private function get_sum_of_votes(): int
    {
        $sum = 0;
        foreach($this->survey->boardgames_and_votes as $votes) 
        {
            $sum += $votes;
        }
        return $sum;
    }

    private function get_vote_leaders(): array
    {
        // determine highest vote count
        $highest_vote_count = 0;
        foreach($this->survey->boardgames_and_votes as $votes) 
        {
            if($votes > $highest_vote_count) 
            {
                $highest_vote_count = $votes;
            }
        }

        $leaders = array();
        foreach($this->survey->boardgames_and_votes as $bgg_id => $votes) 
        {
            if($votes == $highest_vote_count)
            {
                array_push($leaders, $bgg_id);
            }
        }

        return $leaders;
    }
}