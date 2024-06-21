<?php

use MagicObject\Request\InputPost;

require_once dirname(__DIR__) . "/inc.app/app.php";

$inputPost = new InputPost();

error_log($inputPost);
if(!file_exists($inputPost->getDirectory()))
{
    mkdir($inputPost->getDirectory(), 0755, true);
}