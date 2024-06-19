<?php

use MagicObject\Generator\PicoDatabaseDump;
use MagicObject\Request\InputPost;

require_once dirname(__DIR__) . "/inc.app/app.php";
require_once dirname(__DIR__) . "/inc.app/sessions.php";
require_once dirname(__DIR__) . "/inc.app/database.php";

$inputPost = new InputPost();

try
{
	$baseDirectory = $appConfig->getApplication()->getEntityBaseDirectory();
    $baseEntity = $appConfig->getApplication()->getBaseEntityNamespace();
    $baseEntity = str_replace("\\\\", "\\", $baseEntity);
    $baseDir = rtrim($baseDirectory, "\\/")."/".str_replace("\\", "/", trim($baseEntity, "\\/"));
    
    $allQueries = array();

    if($inputPost->getEntity() != null && $inputPost->countableEntity())
    {
        $inputEntity = $inputPost->getEntity();
        foreach($inputEntity as $entityName)
        {
            $entityName = trim($entityName);
            $path = $baseDir."/".$entityName.".php";
            $entityQueries = array();
            if(file_exists($path))
            {
                include_once $path;
                
                $className = "\\".$baseEntity."\\".$entityName;
                $entity = new $className(null, $database);
                $dumper = new PicoDatabaseDump();
	
                $quertArr = $dumper->createAlterTableAdd($entity);
                foreach($quertArr as $sql)
                {
                    $entityQueries[] = $sql."";
                }
                if(!empty($entityQueries))
                {
                    $allQueries[] = "-- SQL for $entityName begin";
                    $allQueries[] = implode("\r\n", $entityQueries);
                    $allQueries[] = "-- SQL for $entityName end\r\n";
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
