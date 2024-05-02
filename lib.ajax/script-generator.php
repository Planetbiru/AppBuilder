<?php

use AppBuilder\AppBuilder;
use AppBuilder\AppBuilderApproval;
use AppBuilder\AppField;
use AppBuilder\AppSecretObject;
use AppBuilder\AppSection;
use AppBuilder\Base\AppBuilderBase;
use MagicObject\MagicObject;
use MagicObject\Request\InputGet;
use MagicObject\Request\InputPost;
use MagicObject\Util\ClassUtil\PicoObjectParser;
use MagicObject\Util\PicoGenericObject;

require_once dirname(__DIR__) . "/inc.app/app.php";

$inputGet = new InputGet();
//$request = new InputPost(true);

$request = PicoObjectParser::parseJsonRecursive(json_decode(file_get_contents("input2.json")));
//echo $request;
//error_log(print_r($request, true));

if($request->issetFields())
{
    $insertFields = array();
    $editFields = array();
    foreach($request->getFields() as $index=>$value)
    {
        $field = new AppField($value);
        if($value->getIncludeInsert())
        {
            $insertFields[$field->getFieldName()] = $field;
        }
        if($value->getIncludeEdit())
        {
            $editFields[$field->getFieldName()] = $field;
        }
    }
    
    $entity = $request->getEntity();
    
    $entityMain = $entity->getMainEntity();
    
    $entityApproval = new MagicObject();
    $entityTrash = new MagicObject();

    $entityMainName = $entityMain->getEntityName();
    
    $approvalRequired = $entity->getApprovalRequired();
    $trashRequired = $entity->getTrashRequired();
    
    $activationKey = $entityInfo->getActive();
    $pkName = $request->getPrimaryKeyName();
    $withStrash = $request->getWithTrash();
    $entityTrashName = $request->getEntityTrash();
    $pkApprovalName = $request->getPrimaryKeyApprovalName();
    
    $appConf = new AppSecretObject($appConfig->getApplication());
    
    $uses = array();
    $uses[] = "use MagicObject\\MagicObject;";
    $uses[] = "use MagicObject\\SetterGetter;";
    $uses[] = "use MagicObject\\Database\\PicoPredicate;";
    $uses[] = "use MagicObject\\Database\\PicoSort;";
    $uses[] = "use MagicObject\\Database\\PicoSortable;";
    $uses[] = "use MagicObject\\Database\\PicoSpecification;";
    $uses[] = "use MagicObject\\Request\\PicoFilterConstant;";
    $uses[] = "use MagicObject\\Request\\InputGet;";
    $uses[] = "use MagicObject\\Request\\InputPost;";
    $uses[] = "use MagicObject\\Util\\AttrUtil;";
    $uses[] = "use AppBuilder\\PicoApproval;";
    $uses[] = "use AppBuilder\\UserAction;";
    $uses[] = "use ".$appConf->getEntityBaseNamespace()."\\$entityMainName;";
    
    if($approvalRequired)
    {
        $entityApproval = $entity->getApprovalEntity();
        $entityApprovalName = $entityApproval->getEntityName();
        $uses[] = "use ".$appConf->getEntityBaseNamespace()."\\$entityApprovalName;";
    }
    
    if($trashRequired)
    {
        $entityTrash = $entity->getTrashEntity();
        $entityTrashName = $entityTrash->getEntityName();
        $uses[] = "use ".$appConf->getEntityBaseNamespace()."\\$entityTrashName;";
    }
    
    $uses[] = "";
    
    $includes = array();
    
    $includeDir = trim($appConf->getBaseIncludeDirectory(), "/\\");
    if(!empty($includeDir))
    {
        $includeDir = '"/'.$includeDir.'/auth.php"';
    }
    else 
    {
        $includeDir = '"auth.php"';
    }
    
    $includes[] = "require_once __DIR__ . $includeDir;";
    $includes[] = "";
    
    $usesSection = implode("\r\n", $uses);
    $includeSection = implode("\r\n", $includes);
    
    $declarationSection = implode("\r\n", array(
        AppBuilderBase::VAR."inputGet = new InputGet();",
        AppBuilderBase::VAR."inputPost = new InputPost();",
        ""
    ));

    // prepare CRUD section begin
    if($approvalRequired == 1)
    {
        $appBuilderApv = new AppBuilderApproval($appConfig, $entityInfo, $entityApvInfo);

        // CRUD
        $createSection = $appBuilderApv->createInsertApprovalSection($entityMain, $insertFields, $approvalRequired, $entityApproval);
        $updateSection = $appBuilderApv->createUpdateApprovalSection($entityMain, $editFields, $approvalRequired, $entityApproval);
        $activationSection = $appBuilderApv->createActivationApprovalSection($entityMain);
        $deactivationSection = $appBuilderApv->createDeactivationApprovalSection($entityMain);     
        $deleteSection = $appBuilderApv->createDeleteApprovalSection($entityMain);
        $approvalSection = $appBuilderApv->createApprovalSection($entityMain, $editFields, $approvalRequired, $entityApproval, $trashRequired, $entityTrash);
        $rejectionSection = $appBuilderApv->createRejectionSection($entityMain, $approvalRequired, $entityApproval);  
        
        // GUI
        $guiInsert = $appBuilderApv->createGuiInsert($entityMain, $insertFields, $approvalRequired, $entityApproval); 
    }
    else
    {
        $appBuilder = new AppBuilder($appConfig, $entityInfo, $entityApvInfo);

        // CRUD
        $createSection = $appBuilder->createInsertSection($entityMain, $insertFields);
        $updateSection = $appBuilder->createUpdateSection($entityMain, $editFields);
        $activationSection = $appBuilder->createActivationSection($entityMain, $activationKey);
        $deactivationSection = $appBuilder->createDeactivationSection($entityMain, $activationKey);
        
        if($withStrash == 1)
        {
            $deleteSection = $appBuilder->createDeleteSection($entityMain, true, $entityTrash);
        }
        else
        {
            $deleteSection = $appBuilder->createDeleteSection($entityMain);
        }
        $approvalSection = "";
        $rejectionSection = "";
    }
    
   
    
    // prepare CRUD section end
    
    $crudSection = (new AppSection(AppSection::SEPARATOR_IF_ELSE))
    ->add($createSection)
    ->add($updateSection)
    ->add($activationSection)
    ->add($deactivationSection)
    ->add($deleteSection)
    ->add($approvalSection)
    ->add($rejectionSection)
    ;
        
    $guiSection = (new AppSection(AppSection::SEPARATOR_IF_ELSE))
    ->add($guiInsert)
    ;

    $merged = (new AppSection(AppSection::SEPARATOR_NEW_LINE))
    ->add($usesSection)
    ->add($includeSection)
    ->add($declarationSection)
    ->add($crudSection)
    ->add($guiSection)
    ;

    
    $fp = fopen(dirname(__DIR__)."/test.php", "w");
    fputs($fp, "<"."?php\r\n\r\n".$merged."\r\n\r\n");
    fclose($fp);
    
}
