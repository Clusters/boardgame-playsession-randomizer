<?php

require_once("./libs/Collection.php");

abstract class WebPageSkeleton
{
    protected function generate_header(string $title, string $redirect_target = ""): string
    {
        $redirection = "";
        if ($redirect_target != "")
        {
            $redirection = "<meta http-equiv=\"Refresh\" content=\"3; url='$redirect_target'\">";
        }

        return "
        <header>
            <title>
                $title
            </title>
            $redirection
        </header>
        ";
    }

    protected function generate_body_encapsulation(string $content): string
    {
        return "
        <body>
            $content
        </body>
        ";
    }
}
?>