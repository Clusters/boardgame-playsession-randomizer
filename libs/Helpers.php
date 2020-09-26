<?php
/**
 * Fetches all surveys and returns an array of Survey objects.
 * 
 * @return:
 * array of Survey with their survey_id as keys
 */
function fetch_all_surveys(): array
{
    if(!is_dir("./resources") || !file_exists("./resources/surveys.json"))
    {
        error_log("Warning: Tried to fetch surveys while surveys.json not yet existing.");
        return array();
    }

    $surveys_json = json_decode(file_get_contents("./resources/surveys.json"), true)["surveys"];

    $surveys = array();
    foreach($surveys_json as $survey_id => $survey_json)
    {
        $surveys[$survey_id] = new Survey($survey_id, $survey_json);
    }

    return $surveys;
}

/**
 * Fetches all boardgames and returns an array of Boardgame objects.
 * 
 * @return:
 * array of Boardgame with their bgg_id as keys
 */
function fetch_all_boardgames(): array
{
    if(!is_dir("./resources") || !file_exists("./resources/boardgames.json"))
    {
        error_log("Warning: Tried to fetch board games while boardgames.json not yet existing.");
        return array();
    }

    $boardgames_array = json_decode(file_get_contents("./resources/boardgames.json"), true);

    $boardgames = array();
    foreach($boardgames_array as $boardgame)
    {
        $boardgames[$boardgame["bgg_id"]] = new Boardgame(
            $boardgame["title"], $boardgame["player_count"], $boardgame["multisession"], $boardgame["tags"],
            $boardgame["preview_url"], $boardgame["tutorial_url"], $boardgame["bgg_id"], $boardgame["created"],
            $boardgame["last_survey_id"], $boardgame["version"]
        );
    }

    return $boardgames;
}
?>