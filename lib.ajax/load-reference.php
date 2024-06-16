<?php

use MagicObject\Request\InputPost;

require_once dirname(__DIR__) . "/inc.app/app.php";

$inputPost = new InputPost();
if($inputPost->getFieldName() != null && $inputPost->getKey() != null)
{
    header("Content-type: application/json");
    $path = $inputPost->getFieldName() . "-" . $inputPost->getKey() . ".json";
    error_log('PATH = '.$path);
    if(file_exists($path))
    {
        echo file_get_contents($path);
    }
    else
    {
        echo "null";
    }
}
