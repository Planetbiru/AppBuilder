<?php

use AppBuilder\AppBuilder;
use AppBuilder\AppBuilderApproval;
use AppBuilder\AppFeatures;
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
$request = new InputPost(true);
if($request->issetFields())
{
    $insertFields = array();
    $editFields = array();
    $detailFields = array();
    $referenceEntity = array();
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
        if($value->getIncludeDetail())
        {
            $detailFields[$field->getFieldName()] = $field;
        }
        if($value->getReference() != null 
        && $value->getReference()->getType() == 'entity' 
        && $value->getReference()->getEntity() != null 
        && $value->getReference()->getEntity()->getEntityName() != null)
        {
            $referenceEntity[] = $value->getReference()->getEntity()->getEntityName();
        }
    }
    
    $entity = $request->getEntity();
    
    $entityMain = $entity->getMainEntity();
    
    $entityApproval = new MagicObject();
    $entityTrash = new MagicObject();

    $entityMainName = $entityMain->getEntityName();
    $approvalRequired = AppBuilderBase::isTrue($entity->getApprovalRequired());
    $trashRequired = AppBuilderBase::isTrue($entity->getTrashRequired());
    
    $activationKey = $entityInfo->getActive();

    
    $appConf = new AppSecretObject($appConfig->getApplication());
    
    $uses = array();
    $uses[] = "// This script is generated automaticaly by AppBuilder";
    $uses[] = "// Visit https://github.com/Planetbiru/AppBuilder";
    $uses[] = "";
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
    $uses[] = "use AppBuilder\\AppInclude;";
    $uses[] = "use AppBuilder\\EntityLabel;";
    
    
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
    foreach($referenceEntity as $ref)
    {
        $uses[] = "use ".$appConf->getEntityBaseNamespace()."\\$ref;";
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
    
    $appFeatures = new AppFeatures($request->getFeatures());

    // prepare CRUD section begin
    if($appFeatures->isApprovalRequired())
    {
        $appBuilder = new AppBuilderApproval($builderConfig, $appConfig, $appFeatures, $entityInfo, $entityApvInfo);

        // CRUD
        $createSection = $appBuilder->createInsertApprovalSection($entityMain, $insertFields, $approvalRequired, $entityApproval);
        $updateSection = $appBuilder->createUpdateApprovalSection($entityMain, $editFields, $approvalRequired, $entityApproval);
        $activationSection = $appBuilder->createActivationApprovalSection($entityMain);
        $deactivationSection = $appBuilder->createDeactivationApprovalSection($entityMain);     
        $deleteSection = $appBuilder->createDeleteApprovalSection($entityMain);
        $approvalSection = $appBuilder->createApprovalSection($entityMain, $editFields, $approvalRequired, $entityApproval, $trashRequired, $entityTrash);
        $rejectionSection = $appBuilder->createRejectionSection($entityMain, $approvalRequired, $entityApproval);  
        
        // GUI
        $guiInsert = $appBuilder->createGuiInsert($entityMain, $insertFields, $approvalRequired, $entityApproval); 
        $guiUpdate = $appBuilder->createGuiUpdate($entityMain, $editFields, $approvalRequired, $entityApproval); 
        $guiDetail = $appBuilder->createGuiDetail($entityMain, $detailFields, $approvalRequired, $entityApproval); 
    }
    else
    {
        $appBuilder = new AppBuilder($builderConfig, $appConfig, $appFeatures, $entityInfo, $entityApvInfo);

        // CRUD
        $createSection = $appBuilder->createInsertSection($entityMain, $insertFields);
        $updateSection = $appBuilder->createUpdateSection($entityMain, $editFields);
        $activationSection = $appBuilder->createActivationSection($entityMain, $activationKey);
        $deactivationSection = $appBuilder->createDeactivationSection($entityMain, $activationKey);
        
        if($trashRequired)
        {
            $deleteSection = $appBuilder->createDeleteSection($entityMain, true, $entityTrash);
        }
        else
        {
            $deleteSection = $appBuilder->createDeleteSection($entityMain);
        }
        $approvalSection = "";
        $rejectionSection = "";
        $guiInsert = $appBuilder->createGuiInsert($entityMain, $insertFields); 
        $guiUpdate = $appBuilder->createGuiUpdate($entityMain, $editFields); 
        $guiDetail = $appBuilder->createGuiDetail($entityMain, $detailFields); 
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
    ->add($guiUpdate)
    ->add($guiDetail)
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
    
    
    require_once dirname(__DIR__) . "/inc.app/database.php";
        
    $appBuilder->generateMainEntity($database, $builderConfig, $appConf, $entityMain, $entityInfo);
    $appBuilder->generateApprovalEntity($database, $builderConfig, $appConf, $entityMain, $entityInfo, $entityApproval);
    $appBuilder->generateTrashEntity($database, $builderConfig, $appConf, $entityMain, $entityInfo, $entityTrash);
    
}


