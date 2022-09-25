<?php
require_once("./libs/WebPageSkeleton.php");
require_once("./libs/WebPage.php");

class ShowBoardgamesPage extends WebPageSkeleton implements WebPage
{
    public function print_page_header()
    {
        echo $this->generate_header("List of all entered board games");
    }

    public function print_page_content()
    {
        $boardgames = fetch_all_boardgames();

        $link_list = "";
        $boardgame_page = Page::ShowBoardgameDetails;
        $boardgame_edit_page = Page::EditBoardgame;
        $boardgame_delete_page = Page::DeleteBoardgame;
        $boardgame_toggle_activation = Page::ToggleBoardgameActivation;
        foreach($boardgames as $bgg_id => $boardgame)
        {
            if(!($boardgame instanceof Boardgame))
            {
                $type=get_class($boardgame);
                die("Error: Wrong item type $type received from fetch_all_boardgames()!");
            }
            
            $boardgame_detail_link = "<a href=\"./index.php?page=$boardgame_page&boardgame_id=$bgg_id\">Show game page</a>";

            if(!($boardgame->active))
            {
                $boardgame_detail_link="<img src=\"./resources/img/disabled.png\" width=\"20px\" height=\"20px\" title=\"Survey expired\">&nbsp;".$boardgame_detail_link;
            }

            $link_list .= <<<HYPERLINK
                <p class="boardgame_item">
                    <label for="$bgg_id">{$boardgame->title}:</label>&nbsp;
                    $boardgame_detail_link&nbsp;|&nbsp;
                    <a href="./index.php?page=$boardgame_edit_page&boardgame_id=$bgg_id">edit</a>&nbsp;|&nbsp;
                    <a href="./index.php?page=$boardgame_delete_page&boardgame_id=$bgg_id">delete</a>&nbsp;|&nbsp;
                    <a href="./index.php?page=$boardgame_toggle_activation&boardgame_id=$bgg_id">disable/enable</a>
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