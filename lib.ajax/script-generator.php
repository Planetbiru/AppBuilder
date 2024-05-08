<?php

use AppBuilder\AppBuilder;
use AppBuilder\AppBuilderApproval;
use AppBuilder\AppFeatures;
use AppBuilder\AppField;
use AppBuilder\AppSecretObject;
use AppBuilder\AppSection;
use AppBuilder\Base\AppBuilderBase;
use AppBuilder\Generator\ScriptGenerator;
use MagicObject\MagicObject;
use MagicObject\Request\InputGet;
use MagicObject\Request\InputPost;

require_once dirname(__DIR__) . "/inc.app/app.php";

$inputGet = new InputGet();
if(isset($_POST) && !empty($_POST))
{
    $request = new InputPost(true);
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
    $moduleName = "test";
    $scriptGenerator->generate($database, $moduleName, $request, $builderConfig, $appConfig, $entityInfo, $entityApvInfo);
    
}


