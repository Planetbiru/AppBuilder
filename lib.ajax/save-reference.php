<?php

use MagicObject\Request\InputPost;

require_once dirname(__DIR__) . "/inc.app/app.php";

$inputPost = new InputPost();
// fieldName, key, value
error_log($inputPost);
if($inputPost->getFieldName() != null && $inputPost->getKey() != null && $inputPost->getValue() != null)
{
    $path = $inputPost->getFieldName() . "-" . $inputPost->getKey() . ".json";
    error_log('PATH = '.$path);
    file_put_contents($path, $inputPost->getValue());
}
