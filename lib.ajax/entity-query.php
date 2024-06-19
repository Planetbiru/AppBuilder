<?php

use MagicObject\Generator\PicoDatabaseDump;
use MagicObject\Request\InputGet;

require_once dirname(__DIR__) . "/inc.app/app.php";
require_once dirname(__DIR__) . "/inc.app/sessions.php";
require_once dirname(__DIR__) . "/inc.app/database.php";

$inputGet = new InputGet();

try
{
	$baseDirectory = $appConfig->getApplication()->getEntityBaseDirectory();
    $baseEntity = $appConfig->getApplication()->getBaseEntityNamespace();
    $baseEntity = str_replace("\\\\", "\\", $baseEntity);
    $baseDir = rtrim($baseDirectory, "\\/")."/".str_replace("\\", "/", trim($baseEntity, "\\/"));
    
    $allQueries = array();

    if($inputGet->countableEntity())
    {
        $inputEntity = $inputGet->getEntity();
        foreach($inputEntity as $entityName)
        {
            $entityName = trim($entityName);
            $path = $baseDir."/".$entityName.".php";
            if(file_exists($path))
            {
                include_once $path;

                $className = "\\".$baseEntity."\\".$entityName;
                $entity = new $className(null, $database);
                $dumper = new PicoDatabaseDump();
	
                $quertArr = $dumper->createAlterTableAdd($entity);
                foreach($quertArr as $sql)
                {
                    $allQueries[] = $sql."";
                }
            }
        }
    }

    echo nl2br(implode("\r\n\r\n", $allQueries));

}
catch(Exception $e)
{
    error_log($e->getMessage());
    // do nothing
}
