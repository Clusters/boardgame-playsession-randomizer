<?php
require_once("./libs/WebPageSkeleton.php");
require_once("./libs/WebPage.php");

class ShowBoardgamesPage extends WebPageSkeleton implements WebPage
{
    public function print_page_header()
    {
        echo $this->generate_header("List of all boardgames");
    }

    public function print_page_content()
    {
        $boardgames = fetch_all_boardgames();

        $link_list = "";
        $boardgame_page = Page::ShowBoardgameDetails;
        foreach($boardgames as $boardgame_id => $boardgame)
        {
            if(!($boardgame instanceof Boardgame))
            {
                $type=get_class($boardgame);
                die("Error: Wrong item type $type received from fetch_all_boardgames()!");
            }

            $link_list .= <<<HYPERLINK
                <p class="survey_item">
                    <label for="$boardgame_id">{$boardgame->title}:</label>&nbsp;
                    <a name="$boardgame_id" href="./index.php?page=$boardgame_page&boardgame_id=$boardgame_id">Show details</a>
                </p>
HYPERLINK;
        }

        $content = <<<HTML
        <p>
            $link_list
        </p>
HTML;
        echo $this->generate_body_encapsulation($content);
    }
}