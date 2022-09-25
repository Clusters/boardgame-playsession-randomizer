<?php
class Boardgame {
    public string $title = "";
    public array$player_count = array();
    public bool $multisession = false;
    public array $tags = array();
    public string $preview_url = "";
    public string $tutorial_url = "";
    public int $bgg_id = 0;
    public bool $active = True;
    private int $version = 1; // version to trace changes in properties etc.
    private int $created = 0;
    private int $last_survey_id = 0;

    function __construct(
            string $title, array $player_count, bool $multisession, array $tags, string $preview_url, string $tutorial_url, 
            int $bgg_id, int $created = null, int $last_survey_id = 0, int $version = null
        ) {
        $this->title = $title;
        $this->player_count = $player_count;
        $this->multisession = $multisession;
        $this->tags = $tags;
        $this->preview_url = $preview_url;
        $this->tutorial_url = $tutorial_url;
        $this->bgg_id = $bgg_id;
        if(is_null($created)) {
            $this->created = time();
        } else {
            $this->created = $created;
        }
        $this->last_survey_id = $last_survey_id;
        if(!is_null($version)) 
        {
            $this->version = $version;
        }
    }

    public function write_boardgame_to_json()
    {
        $boardgame_array = array(
            "title" => $this->title, "player_count" => $this->player_count, "multisession" => $this->multisession, "tags" => $this->tags, 
            "preview_url" => $this->preview_url, "tutorial_url" => $this->tutorial_url, "bgg_id" => $this->bgg_id, "created" => $this->created,
            "last_survey_id" => $this->last_survey_id, "version" => $this->version, "active" => $this->active
        );

        // load/create boardgames.json and insert new boardgame
        if(!file_exists("./data/boardgames.json"))
        {
            $json = json_encode(array($boardgame_array["bgg_id"] => $boardgame_array));
        } else {
            $boardgames = json_decode(file_get_contents("./data/boardgames.json"), true);
            $boardgames[$boardgame_array["bgg_id"]] = $boardgame_array;
            $json = json_encode($boardgames);
        }

        // save new boardgame
        if(!is_dir("./data")){ //Check if the directory already exists.
            //Directory does not exist, so lets create it.
            mkdir("./data", 0755);
        }
        $result = file_put_contents("./data/boardgames.json", $json);
        if(!$result) {
            die("Error: New board game entry could not be saved!");
        } else {
            echo <<<SUCCESS
                <p class="success">New board game created</p>
SUCCESS;
        }
    }
}
?>