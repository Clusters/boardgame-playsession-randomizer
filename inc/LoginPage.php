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
        $payload = Payload::Login;
        $action = Page::LoginVerification;
        $content = <<<HTML
        <form method="POST" action="index.php?page=$action" target="_self">
            <p>
                <input name="payload" type="hidden" value="$payload">
                <label for="password">Login password:</label><br>
                <input name="password" type="password" maxlength="40"><br>
                <br>
                <input type="submit" value="Submit">
            </p>
        </form>
HTML;
        echo $this->generate_body_encapsulation($content);
    }
}
?>