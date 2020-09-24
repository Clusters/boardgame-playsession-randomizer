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
        $content = file_get_contents("./inc/html/set_admin_password_form.html");
        echo $this->generate_body_encapsulation($content);
    }
}
?>