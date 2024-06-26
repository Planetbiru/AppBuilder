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

    echo '<input type="checkbox" id="entity-check-controll" checked> <label for="entity-check-controll">Select all</label>';

    foreach($list as $idx=>$file)
    {
        $entity = basename($file, '.php');
        $li[] = '<li class="entity-li"><input type="checkbox" class="entity-checkbox" name="entity['.$idx.']" value="'.$entity.'" checked> <a href="#" data-entity-name="'.$entity.'">'.$entity.'</a></li>';
    }
    echo '<ul class="entity-ul">'."\r\n\t".implode("\r\n\t", $li)."\r\n".'</ul>';
}
catch(Exception $e)
{
    error_log($e->getMessage());
    // do nothing
}
