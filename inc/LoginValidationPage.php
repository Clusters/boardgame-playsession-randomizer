<?php
require_once("./libs/WebPageSkeleton.php");
require_once("./libs/WebPage.php");

class LoginValidationPage extends WebPageSkeleton implements WebPage {
    private $authenticated = false;

    function __construct(bool $authenticated)
    {
        $this->authenticated = $authenticated;
    }

    public function print_page_header()
    {
        if($this->authenticated)
        {
            echo $this->generate_header("Login Validation", "index.php?page=StartNewSurvey");
        } else {
            echo $this->generate_header("Login Validation", "index.php?page=Login");
        }
        
    }

    public function print_page_content()
    {
        if($this->authenticated) 
        {
            $content = "
            <p>If automated forwarding does not work -> <a href=\"index.php?page=StartNewSurvey\">Click here</a></p>
            ";
        } else {
            $content = "
            <p>If automated forwarding does not work -> <a href=\"index.php?page=Login\">Click here</a></p>
            ";
        }
        echo $this->generate_body_encapsulation($content);
    }
}
?>