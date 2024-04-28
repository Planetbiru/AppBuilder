<?php
use AppBuilder\AppBuilder;
use MagicObject\SecretObject;

require_once dirname(__DIR__) . "/inc.app/app.php";
require_once dirname(__DIR__) . "/inc.app/sessions.php";

try
{
	$conf = new SecretObject($_POST);
	$appId = $conf->getCurrentApplication();

	$appConfig = AppBuilder::loadOrCreateConfig($appId, $appBaseConfigPath, $configTemplatePath); 
	$appConfig->setDatabase($conf->getDatabase());
	$appConfig->setSessions($conf->getSessions());
	$appConfig->setEntityInfo($conf->getEntityInfo());
	$path = $appBaseConfigPath."/".$appId."/default.yml";
	file_put_contents($path, $appConfig->dumpYaml());
}
catch(Exception $e)
{
	echo $e->getMessage();
}
