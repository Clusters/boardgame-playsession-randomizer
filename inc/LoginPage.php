<?php
require_once("./libs/WebPageSkeleton.php");
require_once("./libs/WebPage.php");

class LoginPage extends WebPageSkeleton implements WebPage {
    public function print_page_header()
    {
        echo $this->generate_header("Login");
    }

    public function print_page_content()
    {
        $content = file_get_contents("./inc/html/login_form.html");
        echo $this->generate_body_encapsulation($content);
    }
}
?>