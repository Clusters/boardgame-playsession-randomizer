<?php
require_once("./libs/WebPageSkeleton.php");
require_once("./libs/WebPage.php");

class ShowSurveyPage extends WebPageSkeleton implements WebPage
{
    private $survey_id = 0;

    function __construct(int $survey_id)
    {
        $this->survey_id = $survey_id;
    }

    public function print_page_header()
    {
        echo $this->generate_header("Vote now");
    }

    public function print_page_content()
    {
        $vote_amount = 2; // currently fixed, in future configurable per survey

        $survey = $this->retrieve_survey();
        $boardgames = $this->retrieve_games_from_survey($survey);

        $boardgames_details_page = Page::ShowBoardgameDetails;
        $vote_items = "";
        foreach($boardgames as $bgg_id => $boardgame)
        {
            $multisession = "";
            if($boardgame->multisession) 
            {
                $multisession .= "*";
            }

            $vote_items .= <<<HYPERLINK
                <input class="vote_checkbox vote_item" type="checkbox" name="vote[]" value="$bgg_id">
                <label><a href="./index.php?page=$boardgames_details_page&boardgame_id=$bgg_id">$boardgame->title</a>$multisession</label><br>
HYPERLINK;
        }
        
        $survey_results_page = Page::ShowSurveyResults;
        $payload = Payload::NewVote;
        $plural_s = "";
        if($vote_amount > 1) { $plural_s = "s"; }
        $content = <<<HTML
            <form method="POST" target="_self" action="./index.php?page=$survey_results_page&survey_id=$this->survey_id">
                <input name="payload" type="hidden" value="$payload">
                <input name="survey_id" type="hidden" value="$this->survey_id">
                <h1>Vote for up to $vote_amount board game{$plural_s}</h1>
                <div>
                    $vote_items
                </div>
                <br>
                <input type="submit" value="Submit">
            </form>
            <span id="multisession-footnote">* Multisession board game</span>
            <script type="text/javascript">//<![CDATA[
                var limit = $vote_amount;
                $('input.vote_checkbox').on('change', function(evt) {
                    if($(this).siblings(':checked').length >= limit) {
                        this.checked = false;
                    }
                });
            //]]></script>
HTML;
        echo $this->generate_body_encapsulation($content);
    }

    private function retrieve_survey(): Survey
    {
        $all_surveys = fetch_all_surveys();

        if(!key_exists($this->survey_id, $all_surveys))
        {
            die("Error: An unknown survey_id '$this->survey_id' was supplied in the GET request.");
        }

        return $all_surveys[$this->survey_id];
    }

    /**
     * Retrieves Boardgame objects for the games found in supplied Survey
     * 
     * @return:
     * Returns an array containing all Boardgames of a Survey
     */
    private function retrieve_games_from_survey(Survey $survey): array
    {
        $all_boardgames = fetch_all_boardgames();

        $survey_boardgames = array();
        foreach(array_keys($survey->boardgames_and_votes) as $bgg_id) 
        {
            if(!key_exists($bgg_id, $all_boardgames))
            {
                die("Error: An unknown or deleted Boardgame via bgg_id '$bgg_id' was requested.");
            }

            $survey_boardgames[$bgg_id] = $all_boardgames[$bgg_id];
        }

        return $survey_boardgames;
    }
}