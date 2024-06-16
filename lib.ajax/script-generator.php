<?php

use AppBuilder\Generator\ScriptGenerator;
use MagicObject\MagicObject;
use MagicObject\Request\InputGet;
use MagicObject\Request\InputPost;

require_once dirname(__DIR__) . "/inc.app/app.php";

$inputGet = new InputGet();
if(isset($_POST) && !empty($_POST))
{
    $request = new InputPost(true);
    file_put_contents("coba.json", $request);
}
else
{
    $request = new MagicObject();
    $request->loadJsonFile(dirname(__DIR__)."/input2.json", false, true, true);
}

if($request->issetFields())
{
    require_once dirname(__DIR__) . "/inc.app/database.php";

    $scriptGenerator = new ScriptGenerator();
    $scriptGenerator->generate($database, $request, $builderConfig, $appConfig, $entityInfo, $entityApvInfo);
    
}


