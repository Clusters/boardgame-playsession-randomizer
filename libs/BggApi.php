<?php 
class BggApi
{
    private $bgg_id = null;
    private $bgg_xml = null;

    function __construct(int $bgg_id) {
        
        $this->bgg_id = $bgg_id;

        // load game xml
        $bgg_raw_xml = file_get_contents('https://api.geekdo.com/xmlapi2/thing?id='.$this->bgg_id);

        // transform xml to array
        // Convert xml string into an object 
        $new = simplexml_load_string($bgg_raw_xml); 
        // Convert into json 
        $con = json_encode($new); 
        // Convert into associative array 
        $this->bgg_xml = json_decode($con, true); 

        if(!$this->is_valid_game_on_bgg())
        {
            die("Error: Invalid boardgamegeek id received: $this->bgg_xml");
        }
    }

    public function get_game_tumbnail_url(): string
    {
        return $this->bgg_xml["item"]["thumbnail"];
    }

    public function get_game_description(): string
    {
        return $this->bgg_xml["item"]["description"];
    }

    public function get_game_release_year(): int
    {
        return (int)$this->bgg_xml["item"]["yearpublished"]["@attributes"]["value"];
    }

    public function get_bgg_game_url(): string
    {
        return "https://boardgamegeek.com/boardgame/$this->bgg_id/";
    }

    public function get_game_min_player_count(): int
    {
        return (int)$this->bgg_xml["item"]["minplayers"]["@attributes"]["value"];
    }

    public function get_game_max_player_count(): int
    {
        return (int)$this->bgg_xml["item"]["maxplayers"]["@attributes"]["value"];
    }

    public function get_game_picture_url(): string
    {
        return $this->bgg_xml["item"]["image"];
    }

    private function is_valid_game_on_bgg(): bool
    {
        return key_exists("item", $this->bgg_xml);
    }
}
?>