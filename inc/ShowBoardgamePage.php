<?php
require_once("./libs/WebPageSkeleton.php");
require_once("./libs/WebPage.php");
require_once("./libs/BggApi.php");

class ShowBoardgamePage extends WebPageSkeleton implements WebPage
{
    private $boardgame = null;

    function __construct(int $bgg_id)
    {
        $boardgames = fetch_all_boardgames();

        if(!key_exists($bgg_id, $boardgames))
        {
            die("Error: Unknown or deleted boardgame requested.");
        }

        $boardgame = $boardgames[$bgg_id];

        if(!($boardgame instanceof Boardgame))
        {
            $type = get_class($boardgame);
            die("Error: Array item was of type '$type' but expected Boardgame");
        }

        $this->boardgame = $boardgame;
    }

    public function print_page_header()
    {
        $title = $this->boardgame->title;
        echo $this->generate_header("Details of $title");
    }

    public function print_page_content()
    {
        $bgg_api = new BggApi($this->boardgame->bgg_id);
        $image = $bgg_api->get_game_picture_url();
        $thumbnail = $bgg_api->get_game_tumbnail_url();
        $title = $this->boardgame->title;
        $year = $bgg_api->get_game_release_year();
        $bgg_url = $bgg_api->get_bgg_game_url();
        $min_players = $bgg_api->get_game_min_player_count();
        $max_players = $bgg_api->get_game_max_player_count();
        $description = $bgg_api->get_game_description();

        $preview_content = "";
        if($this->boardgame->preview_url != "") {
            if(preg_match("#https:\/\/(www\.)?(youtu\.be|youtube\.com)\/#", $this->boardgame->preview_url) == 1)
            {
                preg_match("#(https:\/\/youtu.be\/|watch\?v=)(?<video_id>[^&\s]+)(&\S+)?#",$this->boardgame->preview_url, $matches);
                $video_id = $matches["video_id"];
                $short_preview_video = "https://www.youtube.com/embed/$video_id";
                $preview_content = <<<VIDEO
                <div class="center preview-video">
                    <label for="short_preview">Short Preview</label><br>
                    <iframe name="short_preview" width="560" height="315" src="$short_preview_video" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                </div>
VIDEO;
            } else {
                $url = $this->boardgame->preview_url;
                $preview_content = <<<LINK
                <div class="center preview-url">
                    <a href="$url" target="_blank">Short Preview</a>
                </div>
LINK;
            }
        }

        $tutorial_content = "";
        if($this->boardgame->tutorial_url != "") {
            // check if it is a Youtube video
            if(preg_match("#https:\/\/(www\.)?(youtu\.be|youtube\.com)\/#", $this->boardgame->tutorial_url) == 1)
            {
                // extract video id
                preg_match("#(https:\/\/youtu.be\/|watch\?v=)(?<video_id>[^&\s]+)(&\S+)?#",$this->boardgame->tutorial_url, $matches);
                $video_id = $matches["video_id"];
                $short_tutorial_video = "https://www.youtube.com/embed/$video_id";
                $tutorial_content = <<<VIDEO
                <div class="center tutorial-video">
                    <label for="tutorial">How to play</label><br>
                    <iframe name="tutorial" width="560" height="315" src="$short_tutorial_video" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                </div>
VIDEO;
            } else {
                // fill in non-Youtube URL as a generic hyperlink
                $url = $this->boardgame->tutorial_url;
                $tutorial_content = <<<LINK
                <div class="center tutorial-url">
                    <a href="$url" target="_blank">How to play</a>
                </div>
LINK;
            }
        }

        $content = <<<HTML
        
        <div class="background-image" style="background-image: url($image);"></div>
        <div class="background-content">
            <div class="tumbnail">
                <img class="center" src="$thumbnail">
            </div>
            <h1 style="text-align: center;">
                $title
            </h1>
            <table class="game-sub-title">
                <tr>
                    <td style="text-align: left; width: 33%;">
                        $year
                    </td>
                    <td style="text-align: center; width: 34%;">
                        <a class="bgg-link" href="$bgg_url" target="_blank">BGG Link</a>
                    </td>
                    <td style="text-align: right; width: 33%;">
                        {$min_players}-{$max_players} players
                    </td>
                </tr>
            </table>
            <pre class="game-description center">
                $description
            </pre>
            <br>
            <br>
            $preview_content
            <br>
            <br>
            $tutorial_content
        </div>

HTML;
        echo $this->generate_body_encapsulation($content);
    }
}