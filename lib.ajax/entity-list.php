<?php

require_once dirname(__DIR__) . "/inc.app/app.php";
require_once dirname(__DIR__) . "/inc.app/sessions.php";

try
{
	$baseDirectory = $appConfig->getApplication()->getEntityBaseDirectory();
    $baseEntity = $appConfig->getApplication()->getBaseEntityNamespace();
    $baseEntity = str_replace("\\\\", "\\", $baseEntity);
    $baseDir = rtrim($baseDirectory, "\\/")."/".str_replace("\\", "/", trim($baseEntity, "\\/"));
    
    $list = glob($baseDir."/*.php");
    $li = array();

    foreach($list as $idx=>$file)
    {
        $entity = basename($file, '.php');
        $li[] = '<li class="entity-li"><a href="#" data-entity-name="'.$entity.'">'.$entity.'</a></li>';
    }
    echo '<ul class="entity-ul">'."\r\n\t".implode("\r\n\t", $li)."\r\n".'</ul>';
}
catch(Exception $e)
{
    error_log($e->getMessage());
    // do nothing
}
