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
            $action = Page::AdminHome;
            echo $this->generate_header("Login Validation", "index.php?page=$action");
        } else {
            $action = Page::Login;
            echo $this->generate_header("Login Validation", "index.php?page=$action");
        }
        
    }

    public function print_page_content()
    {
        if($this->authenticated) 
        {
            $action = Page::AdminHome;
            $content = "
            <p>If automated forwarding does not work -> <a href=\"index.php?page=$action\">Click here</a></p>
            ";
        } else {
            $action = Page::Login;
            $content = "
            <p>If automated forwarding does not work -> <a href=\"index.php?page=$action\">Click here</a></p>
            ";
        }
        echo $this->generate_body_encapsulation($content);
    }
}
?>