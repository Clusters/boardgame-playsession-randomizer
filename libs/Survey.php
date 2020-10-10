<?php
class Survey
{
    private $version = 1; // version to trace changes in properties etc.
    private $last_update = 0;
    public $active = null;
    public $survey_id = 0;
    public $boardgames_and_votes = array();
    private $voted_visitors = array();
    private $voting_counter = 0; # represents the number of already performed user votings for this survey
    private $started_on = null;
    private $runs_until = null;

    function __construct(int $survey_id = 0, array $survey_json = array())
    {
        $this->survey_id = $survey_id;

        if($survey_id != 0)
        {
            if($survey_json == array())
            {
                $this->get_survey_information();
            } else {
                $this->digest_json($survey_json);
            }
            $this->is_still_active();
        }
    }

    /**
     * Fetches informations from surveys.json regarding to the objects $this->survey_id
     * @return:
     * True if update was successful, or False if $this->survey_id was not found or 0
     */
    public function get_survey_information(): bool
    {
        if($this->survey_id == 0)
        {
            error_log("Warning: Tried to update on not yet set survey_id = 0.");
            return false;
        }

        if(!is_dir("./data") || !file_exists("./data/surveys.json"))
        {
            die("Error: Tried to fetch survey information, when there is no surveys.json file yet.");
        }

        $surveys_array = json_decode(file_get_contents("./data/surveys.json"), true)["surveys"];

        if(!array_key_exists($this->survey_id, $surveys_array))
        {
            error_log("Warning: Invalid survey_id $this->survey_id given.");
            return false;
        }

        $survey = $surveys_array[$this->survey_id];

        $this->digest_json($survey);

        $this->last_update = time();

        return true;
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

        $survey_json = $this->generate_new_survey_json($games, $until);

        $this->survey_id = $this->add_new_survey_to_json_file($survey_json);
        $this->active = true;

        echo <<<SUCCESS
            <p class="success">Survey $this->survey_id started</p>
SUCCESS;

        return $this->survey_id;
    }

    /**
     * Parses, adds and updates the votes of a voting of this survey with given votes
     * 
     * @param votes:
     * Expected is an array containing the bgg_ids of the games were votes should be applied.
     */
    public function digest_voting(array $votes) 
    {
        if($this->boardgames_and_votes == array())
        {
            die("Error: This survey has no games to vote on!");
        }

        foreach($votes as $bgg_id)
        {
            $this->boardgames_and_votes[$bgg_id]++;
        }

        $this->voting_counter++;

        if(!key_exists("visitor_id", $_COOKIE))
        {
            die("Error: The visitor id could not be found.");
        }
        array_push($this->voted_visitors, $_COOKIE["visitor_id"]);

        $survey_json = $this->generate_updated_survey_json();
        $this->write_survey_to_json_file($survey_json);

        echo <<<SUCCESS
            <p class="success">Vote was registered successfully</p>
SUCCESS;
    }

    /**
     * Checks if a visitor has already voted on this survey
     * 
     * @param visitor_id:
     * Visitor ID which should be compared with the list of voters
     * 
     * @return:
     * True, if the supplied visitor_id was found inside the list of voters of this survey.
     * Otherwise False.
     */
    public function has_voted(string $visitor_id): bool
    {
        if(in_array($visitor_id, $this->voted_visitors))
        {
            return true;
        }
        return false;
    }

    private function fetch_random_games(int $amount): array
    {
        // fetch all board games
        $boardgames = fetch_all_boardgames();
        
        $boardgames_amount = sizeof($boardgames);
        if($boardgames_amount<$amount)
        {
            die("Error: Tried to start a survey with $amount of games, when there have been only $boardgames_amount board games added yet!");
        }

        $chosen_boardgames = array();
        // choose randomly <amount> of board games
        for($i=0;$i<$amount;$i++)
        {
            // try to find a fitting board game
            while(true)
            {
                if(sizeof($boardgames)<1)
                {
                    die("Error: With applied filters, not enough board games could be found to start the survey. Try again with different filters.");
                }

                $random_bg_index_offset = random_int(0,sizeof($boardgames)-1);
                $random_bg_key = array_keys($boardgames)[$random_bg_index_offset];

                $candidate = $boardgames[$random_bg_key];
                if(!($candidate instanceof Boardgame))
                {
                    $type = get_class($candidate);
                    die("Error: Wrong data type $type received from item of fetch_all_boardgames() return.");
                }

                unset($boardgames[$random_bg_key]); // delete used game from pool of games

                // analyse candidate (apply filters)
                // no filters implemented yet
                array_push($chosen_boardgames, $candidate);
                break;
            }
        }

        return $chosen_boardgames;
    }

    private function generate_new_survey_json(array $games, int $until): array
    {
        $game_items = array();
        foreach($games as $game) {
            $game_items[$game->bgg_id] = 0; // the int represents the amount of votes the game got (0 for new surveys)
        }

        $survey_json = array(
            "games" => $game_items, "started" => time(), "run_until" => $until, "version" => $this->version, "voting_counter" => $this->voting_counter,
            "voters" => $this->voted_visitors
        );
        $this->digest_json($survey_json);
        return $survey_json;
    }

    private function generate_updated_survey_json(): array
    {
        if(is_null($this->started_on) || is_null($this->runs_until))
        {
            die("Error: Incomplete or unloaded survey cannot be updated!");
        }

        return array(
            "games" => $this->boardgames_and_votes, "started" => $this->started_on, "run_until" => $this->runs_until,
            "version" => $this->version, "voting_counter" => $this->voting_counter, "voters" => $this->voted_visitors
        );
    }

    private function add_new_survey_to_json_file(array $survey_json): int
    {
        $new_survey_id = $this->get_last_survey_id()+1;
        $this->write_survey_to_json_file($survey_json, $new_survey_id);

        return $new_survey_id;
    }

    /**
     * Writes a survey in json-like format to the survey.json file. Can used to update existing surveys, but also
     * to write new surveys.
     * 
     * @param survey_json:
     * json-like array in the following format:
     * array (
     *      "games" => array (
     *          "<bgg_id>" => <number_of_votes>,
     *          "151347" => 0,
     *          "180263" => 0,
     *          "181687" => 0
     *      ),
     *      "started" => 1601062011,
     *      "run_until" => 1601493240,
     *      "version" => 1,
     *      "voting_counter" => 0,
     *      "voters" => array(
     *          "arandomvisitorid" => 1601062011, 
     *          "another" => 1601493240
     *      )
     * )
     * @param new_survey_id:
     * Optional: Only necessary if the last_survey_id should be updated. Will also 
     * be used over $this->survey_id
     */
    private function write_survey_to_json_file(array $survey_json, int $new_survey_id = null)
    {
        $survey_id = $this->survey_id;
        if(!is_null($new_survey_id))
        {
            $survey_id = $new_survey_id;
        }

        if(!is_dir("./data") || !file_exists("./data/surveys.json"))
        {
            if(!is_dir("./data"))
            {
                mkdir("./data", 0755);
            }

            $surveys_json = array(
                "last_survey_id" => $survey_id,
                "surveys" => array($survey_id => $survey_json)
            );
        } else {
            $surveys_json = json_decode(file_get_contents("./data/surveys.json"), true);
            $surveys_json["surveys"][$survey_id] = $survey_json;
            if(!is_null($new_survey_id))
            {
                $surveys_json["last_survey_id"] = $new_survey_id;
            }
        }

        $result = file_put_contents("./data/surveys.json", json_encode($surveys_json));
        if(!$result) {
            die("Error: Survey could not written to json file!");
        }
    }

    /**
     * Receives a raw json array object and reads&writes information from it into the object.
     * 
     * Structure of version 1 is:
     * {
     *      "games": {
     *          "<bgg_id>": <number_of_votes>,
     *          "151347": 0,
     *          "180263": 0,
     *          "181687": 0
     *      },
     *      "started": 1601062011,
     *      "run_until": 1601493240,
     *      "version": 1,
     *      "voting_counter": 0,
     *      "voters": {
     *          "aRandomVisitorID": 1601062011,
     *          "another" => 1601493240
     *      }
     *  }
     */
    private function digest_json(array $survey_json)
    {        
        $this->boardgames_and_votes = $survey_json["games"];

        $this->started_on = $survey_json["started"];
        $this->runs_until = $survey_json["run_until"];
        $this->version = $survey_json["version"];
        $this->last_update = time();

        if(key_exists("voting_counter", $survey_json)) 
        {
            $this->voting_counter = $survey_json["voting_counter"];
        } else {
            die("Incompatible survey with found! 'voting_counter' missing");
        }
        if(key_exists("voters", $survey_json))
        {
            $this->voted_visitors = $survey_json["voters"];
        } else {
            die("Incompatible survey found! 'voters' missing");
        }
    }

    /**
     * Checks if the survey is still active or needs to be updated.
     * Triggers the survey update if necessary.
     */
    private function is_still_active(): bool
    {
        if($this->survey_id == 0)
        {
            die("Error: Illegal access to method is_still_active().");
        }

        if(is_null($this->last_update) || time()-$this->last_update > 10 || is_null($this->runs_until))
        {
            $this->get_survey_information();
        }

        if(time() > $this->runs_until)
        {
            $this->active = false;
            return false;
        }
        $this->active = true;
        return true;
    }

    /**
     * @return:
     * Returns the id of the last started survey. Including deleted surveys.
     */
    private function get_last_survey_id(): int
    {
        if(!is_dir("./data") || !file_exists("./data/surveys.json"))
        {
            return 0;
        }

        $surveys_json = json_decode(file_get_contents("./data/surveys.json"), true);

        return $surveys_json["last_survey_id"];
    }
}
?>