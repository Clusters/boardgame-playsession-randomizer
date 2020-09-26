<?php
require_once("./libs/WebPageSkeleton.php");
require_once("./libs/WebPage.php");

class StartSurveyPage extends WebPageSkeleton implements WebPage
{
    public function print_page_header()
    {
        echo $this->generate_header("Start a new survey");
    }

    public function print_page_content()
    {
        $system_tz_offset = date("P");


        $games_amount_options = "";
        $max_games_per_survey = 10;
        for($i=1;$i<$max_games_per_survey+1;$i++)
        {
            $games_amount_options .= <<<OPTION
                <option value="$i">$i</option>
OPTION;
        }
        
        $action = Page::ShowSurveys;
        $payload = Payload::NewSurvey;
        $content = <<<HTML
        <form method="POST" action="index.php?page=$action" target="_self">
            <p>
                <input name="payload" type="hidden" value="$payload">
                <h1>Start new survey</h1>
                <label for="games_amount">How many games should be featured?<span style="color:red;">*</span>:</label><br>
                <select name="games_amount">
                    <option value="-" selected disabled>-</option>
                    $games_amount_options
                </select><br>
                <br>
                <label for="run_until_date">When should the survey close for voting? (default is 5 days)</label><br>
                <input name="run_until_date" type="date" placeholder="YYYY-MM-DD" pattern="\d\d\d\d-\d\d-\d\d" title="date in format YYYY-MM-DD"><span class="spacer"></span>
                <input name="run_until_time" type="time" placeholder="HH:MM" pattern="\d\d:\d\d" title="24h time in format HH:MM"><br>
                UTC<input name="user_timezone" type="text" value="$system_tz_offset" size="3" pattern="[+-]\d\d:\d\d" title="your local time zone offset in format +/-HH:MM" required><br>
                <br>
                <input type="submit" value="Submit">
            </p>
        </form>
HTML;
        echo $this->generate_body_encapsulation($content);
    }
}


?>