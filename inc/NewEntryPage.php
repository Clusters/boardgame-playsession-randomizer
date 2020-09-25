<?php
require_once("./libs/WebPageSkeleton.php");
require_once("./libs/WebPage.php");

class NewEntryPage extends WebPageSkeleton implements WebPage
{
    public function print_page_header()
    {
        echo $this->generate_header("Add new board game.");
    }

    public function print_page_content()
    {
        $playercount_options = "";
        for($i=1;$i<15;$i++) 
        {
            $playercount_options .= <<<OPTION
                <option value="$i">$i</option>
            OPTION;
        }
        $playercount_options_size = 14;

        $tag_options = "";
        for($i=0;$i<sizeof(Lists::AllTags);$i++)
        {
            $tag = Lists::AllTags[$i];
            $tag_options .= <<<OPTION
                <option value="$tag">$tag</option>
            OPTION;
        }
        $tag_options_count = sizeof(Lists::AllTags);

        $payload = Payload::NewEntry;
        $action = Page::NewEntry;
        $content = <<<HTML
        <form method="POST" action="index.php?page=$action" target="_self">
            <p>
                <input name="payload" type="hidden" value="$payload">
                <h1>Add new board game entry</h1>
                <label for="title">Board game name/title (must be unique)<span style="color:red;">*</span>:</label><br>
                <input name="title" type="text" maxlength="40" minlength="1" required><br>
                <br>
                <label for="player-count">Playable with which player counts?</label><br>
                <select name="player-count[]" size="$playercount_options_size" multiple>
                    $playercount_options
                </select><br>
                <br>
                <label for="multisession">Multisession game?</label><span class="spacer"></span><input type="checkbox" name="multisession" value="true"><br>
                <br>
                <label for="tags">Tags</label><br>
                <select name="tags[]" size="$tag_options_count" multiple>
                    $tag_options
                </select><br>
                <br>
                <label for="preview_url">Enter an URL to a short preview (e.g. Youtube Video, Sneak Peak blog entry, etc.):</label><br>
                <input name="preview_url" type="url"><br>
                <br>
                <label for="tutorial_url">Enter an URL to a gameplay tutorial (e.g. Youtube Video, blog entry, etc.):</label><br>
                <input name="tutorial_url" type="url"><br>
                <br>
                <label for="bgg_url">Enter the <a href="https://boardgamegeek.com/advsearch/boardgame">boardgamegeek</a> URL of the game<span style="color:red;">*</span>:</label><br>
                <input name="bgg_url" type="url"><br>
                <br>
                <input type="submit" value="Submit">
            </p>
        </form>
        HTML;
        echo $this->generate_body_encapsulation($content);
    }
}
?>