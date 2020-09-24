<?php

require_once("./boardgame_thursday_loader.php");

if(isset($_POST["payload"])) {
    if(!array_key_exists($_POST["payload"], $all_payload_types)) {
        die("Error: The requested payload is invalid!");
    }
    $received_payload = $all_payload_types[$_POST["payload"]];

    digest_request($received_payload);
}

if(isset($_GET["page"])) {
    if(!array_key_exists($_GET["page"], $all_pages)) {
        die("Error: The requested page is not valid!");
    }
    $requested_page = $all_pages[$_GET["page"]];
} else {
    $requested_page = Page::Login;
}

$page = page_init($requested_page);

?>
<html>
    <?php $page->print_page_header() ?>
    <?php $page->print_page_content() ?>
</html>