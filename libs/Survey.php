<?php
class Survey
{
    private $version = 1; // version to trace changes in properties etc.
    private $active = false;
    private $survey_id = 0;

    function __construct(int $survey_id = 0)
    {
        $this->survey_id = $survey_id;

        if($survey_id != 0)
        {
            $this->check_if_still_active();
        }
    }

    /**
     * This method starts a new survey with a given amount of games
     * @param games_amount:
     * The amount of games which is featured in the survey once started.
     * @param until:
     * A timestamp with the time (in the future) until the survey should be open for votes.
     * @return:
     * Returns the survey id of the newly started survey.
     */
    public function start_survey(int $games_amount, int $until): int
    {
        if(time() > $until)
        {
            die("Error: The given time lies in the past.");
        }

        $games = $this->fetch_random_games($games_amount);

        $survey_json = $this->generate_survey_json($games, $until);

        $this->survey_id = $this->add_new_survey_to_json_file($survey_json);

        echo <<<SUCCESS
            <p class="success">Survey $this->survey_id started</p>
        SUCCESS;

        return $this->survey_id;
    }

    private function fetch_random_games(int $amount): array
    {
        // fetch all board games
        if(!is_dir("./resources") || !file_exists("./resources/boardgames.json"))
        {
            die("Error: Tried to start a survey with $amount of games, when there are no board games added yet!");
        }

        $boardgames = json_decode(file_get_contents("./resources/boardgames.json"), true);
        
        $boardgames_amount = sizeof($boardgames);
        if($boardgames_amount<$amount)
        {
            die("Error: Tried to start a survey with $amount of games, when there have been only $boardgames_amount board games added yet!");
        }

        $chosen_boardgames = array();
        // choose randomly <amount> of board games
        for($i=0;$i<$amount;$i++)
        {
            // try to find a fitting
            while(true)
            {
                if(sizeof($boardgames)<1)
                {
                    die("Error: With applied filters, not enough board games could be found to start the survey. Try again with different filters.");
                }

                $random_bg_index_offset = random_int(0,sizeof($boardgames)-1);
                $random_bg_key = array_keys($boardgames)[$random_bg_index_offset];

                $candidate = new Boardgame(
                    $boardgames[$random_bg_key]["title"], $boardgames[$random_bg_key]["player_count"], $boardgames[$random_bg_key]["multisession"], $boardgames[$random_bg_key]["tags"],
                    $boardgames[$random_bg_key]["preview_url"], $boardgames[$random_bg_key]["tutorial_url"], $boardgames[$random_bg_key]["bgg_id"], $boardgames[$random_bg_key]["created"],
                    $boardgames[$random_bg_key]["last_survey_id"], $boardgames[$random_bg_key]["version"]
                );

                unset($boardgames[$random_bg_key]); // delete used game from pool of games

                // analyse candidate (apply filters)
                // no filters implemented yet
                array_push($chosen_boardgames, $candidate);
                break;
            }
        }

        return $chosen_boardgames;
    }

    private function generate_survey_json(array $games, int $until): array
    {
        $game_items = array();
        foreach($games as $game) {
            $game_items[$game->bgg_id] = 0; // the int represents the amount of votes the game got (0 on generation)
        }

        return array(
            "games" => $game_items, "started" => time(), "run_until" => $until
        );
    }

    private function add_new_survey_to_json_file(array $survey_json): int
    {
        $new_survey_id = $this->get_last_survey_id()+1;
        if(!is_dir("./resources") || !file_exists("./resources/surveys.json"))
        {
            if(!is_dir("./resources"))
            {
                mkdir("./resources", 0755);
            }

            $surveys_json = array(
                "last_survey_id" => $new_survey_id,
                "surveys" => array($new_survey_id => $survey_json)
            );
        } else {
            $surveys_json = json_decode(file_get_contents("./resources/surveys.json"), true);
            $surveys_json["last_survey_id"] = $new_survey_id;
            $surveys_json["surveys"][$new_survey_id] = $survey_json;
        }

        $result = file_put_contents("./resources/surveys.json", json_encode($surveys_json));
        if(!$result) {
            die("Error: Survey could not be started!");
        }

        return $new_survey_id;
    }

    /**
     * Checks if the survey is still active or needs to be updated.
     * Triggers the survey update if necessary.
     */
    private function check_if_still_active()
    {
        if($this->survey_id == 0)
        {
            die("Error: Illegal access to method check_if_still_active().");
        }

        //todo
    }

    private function get_last_survey_id(): int
    {
        if(!is_dir("./resources") || !file_exists("./resources/surveys.json"))
        {
            return 0;
        }

        $surveys_json = json_decode(file_get_contents("./resources/surveys.json"), true);

        return $surveys_json["last_survey_id"];
    }
}
?>