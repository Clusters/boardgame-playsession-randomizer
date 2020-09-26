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

        return <<<HTML
        <head>
            <title>
                $title
            </title>
            <link rel="stylesheet" href="./resources/css/site.css">
            $redirection
            <script type="text/javascript" src="//code.jquery.com/jquery-1.9.1.js"></script>
        </head>
        HTML;
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