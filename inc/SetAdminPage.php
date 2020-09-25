<?php
require_once("./libs/WebPageSkeleton.php");
require_once("./libs/WebPage.php");

class SetAdminPage extends WebPageSkeleton implements WebPage {
    public function print_page_header()
    {
        echo $this->generate_header("Set new administrator password");
    }

    public function print_page_content()
    {
        $action = Page::Login;
        $payload = Payload::NewAdminPassword;
        $content = <<<HTML
        <form method="POST" action="index.php?page=$action" target="self">
            <p>
                <input name="payload" type="hidden" value="$payload">
                <label for="new_password">New administrator password:</label><br>
                <input name="new_password" type="password" maxlength="40" minlength="6" required><br>
                <br>
                <input type="submit" value="Submit">
            </p>
        </form>
        HTML;
        echo $this->generate_body_encapsulation($content);
    }
}
?>