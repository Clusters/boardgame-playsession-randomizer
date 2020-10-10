<?php

/**
 * Check if this is a new visitor, or a returning visitor with expired cookie.
 * Refresh if not.
 */
function visitor_check()
{
    if(!key_exists("visitor_id",$_COOKIE))
    {
        set_new_unique_visitor_id();
    }
    else
    {
        refresh_visitor_id();
    }
}

/**
 * This method sets a cookie containing a unique visitor id and updates the known visitors json file
 */
function set_new_unique_visitor_id()
{
    $known_visitor_ids = fetch_known_visitor_ids();
    $visitor_id = random_int(100000000000000,999999999999999);

    // generate unique visitor ids
    while(true) {
        if(key_exists($visitor_id, $known_visitor_ids)) 
        {
            $visitor_id = random_int(100000000000000,999999999999999);
            continue;
        }
        break;
    }

    // insert new visitor id
    $known_visitor_ids[$visitor_id] = time();
    $json = json_encode($known_visitor_ids);

    // save new visitor id
    if(!is_dir("./data")){ //Check if the directory already exists.
        //Directory does not exist, so lets create it.
        mkdir("./data", 0755);
    }
    $result = file_put_contents("./data/unique_visitors.json", $json);
    if(!$result) {
        die("Error: New visitor id entry could not be saved!");
    }

    setcookie("visitor_id", $visitor_id,  time()+60*60*24*30*12);
}

/**
 * Refresh the current visitor, so he does not loose his cookie
 */
function refresh_visitor_id()
{
    $known_visitor_ids = fetch_known_visitor_ids();
    $known_visitor_ids[$_COOKIE["visitor_id"]] = time();

    $json = json_encode($known_visitor_ids);
    $result = file_put_contents("./data/unique_visitors.json", $json);
    if(!$result) {
        die("Error: Visitor id update could not be saved!");
    }

    setcookie("visitor_id", $_COOKIE["visitor_id"],  time()+60*60*24*30*12);
}

?>