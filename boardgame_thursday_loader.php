<?php

function init() {
    $json_file = file_get_contents('./resources/boardgames.json');

    print_r($json_file);

    $boardgames = json_decode($json_file, true);

    print_r($boardgames);
}

?>