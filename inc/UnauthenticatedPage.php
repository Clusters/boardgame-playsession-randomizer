<?php
require_once("./libs/WebPageSkeleton.php");
require_once("./libs/WebPage.php");

class UnauthenticatedPage extends WebPageSkeleton implements WebPage 
{
    public function print_page_header()
    {
        echo $this->generate_header("Access denied");
    }

    public function print_page_content()
    {
        $content = "
        You are not authenticated to view this page.<br>
        Try to <a href=\"index.php?page=Login\">log in</a>
        ";
        echo $this->generate_body_encapsulation($content);
    }
}

?>