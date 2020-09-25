<?php

require_once("./boardgame_thursday_loader.php");

?>
<html>
<?php

if(isset($_POST["payload"])) {
    if(!array_key_exists($_POST["payload"], Lists::AllPayloadTypes)) {
        die("Error: The requested payload is invalid!");
    }
    $received_payload = Lists::AllPayloadTypes[$_POST["payload"]];

    digest_request($received_payload);
}

if(isset($_GET["page"])) {
    if(!array_key_exists($_GET["page"], Lists::AllPages)) {
        die("Error: The requested page is not valid!");
    }
    $requested_page = Lists::AllPages[$_GET["page"]];
} else {
    $requested_page = Page::Login;
}

$page = page_init($requested_page);

$page->print_page_header(); 
$page->print_page_content(); ?>
</html>