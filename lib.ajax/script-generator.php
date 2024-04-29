<?php

use AppBuilder\AppBuilder;
use AppBuilder\AppBuilderApproval;
use AppBuilder\AppField;
use AppBuilder\AppSecretObject;
use AppBuilder\AppSection;
use MagicObject\Request\InputGet;
use MagicObject\Request\InputPost;

require_once dirname(__DIR__) . "/inc.app/app.php";

$inputGet = new InputGet();
$inputPost = new InputPost();
if($inputPost->issetFields() && $inputPost->countableFields())
{
    $insertFields = array();
    $editFields = array();
    foreach($inputPost->getFields() as $index=>$value)
    {
        $field = new AppField($value);
        if($value['includeInsert'])
        {
            $insertFields[$field->getName()] = $field;
        }
        if($value['includeEdit'])
        {
            $editFields[$field->getName()] = $field;
        }
    }
    
    $entityName = $inputPost->getEntity();
    $requireApproval = $inputPost->getRequireApproval();
    $entityNameApproval = $inputPost->getEntityApproval();
    
    $activationKey = $entityInfo->getActive();
    $pkName = $inputPost->getPrimaryKeyName();
    $withStrash = $inputPost->getWithTrash();
    $entityNameTrash = $inputPost->getEntityTrash();
    $pkApprovalName = $inputPost->getPrimaryKeyApprovalName();
    
    $appConf = new AppSecretObject($appConfig->getApplication());
    
    $uses = array();
    $uses[] = "use ".$appConf->getEntityBaseNamespace()."\\$entityName;";
    $uses[] = "use ".$appConf->getEntityBaseNamespace()."\\$entityNameApproval;";
    $uses[] = "use ".$appConf->getEntityBaseNamespace()."\\$entityNameTrash;";
    $uses[] = "use MagicObject\\MagicObject;";
    $uses[] = "use MagicObject\\SetterGetter;";
    $uses[] = "use MagicObject\\Request\\PicoFilterConstant;";
    $uses[] = "use MagicObject\\Request\\InputGet;";
    $uses[] = "use MagicObject\\Request\\InputPost;";
    $uses[] = "use AppBuilder\\PicoApproval;";
    $uses[] = "use AppBuilder\\UserAction;";
    $uses[] = "";
    
    $usesSection = implode("\r\n", $uses);
    
    $declarationSection = implode("\r\n", array(
        AppBuilderApproval::VAR."inputGet = new InputGet();",
        AppBuilderApproval::VAR."inputPost = new InputPost();",
        ""
    ));

    // prepare CRUD section begin
    if($requireApproval == 1)
    {
        $appBuilderApv = new AppBuilderApproval($database, $appConfig, $entityInfo);

        $createSection = $appBuilderApv->createInsertApprovalSection($entityName, $insertFields, $pkName, $entityNameApproval);
        $updateSection = $appBuilderApv->createUpdateApprovalSection($entityName, $editFields, $pkName, $entityNameApproval, $pkApprovalName);
        $activationSection = $appBuilderApv->createActivationApprovalSection($entityName, $pkName);
        $deactivationSection = $appBuilderApv->createDeactivationApprovalSection($entityName, $pkName);     
        $deleteSection = $appBuilderApv->createDeleteApprovalSection($entityName, $pkName);
        $approvalSection = $appBuilderApv->createApprovalSection($entityName, $pkName, $editFields, $entityNameApproval, $entityNameTrash);
        $rejectionSection = $appBuilderApv->createRejectionSection($entityName, $pkName, $entityNameApproval);  
    }
    else
    {
        $appBuilder = new AppBuilder($database, $appConfig, $entityInfo);

        $createSection = $appBuilder->createInsertSection($entityName, $insertFields);
        $updateSection = $appBuilder->createUpdateSection($entityName, $editFields);
        $activationSection = $appBuilder->createActivationSection($entityName, $pkName, $activationKey);
        $deactivationSection = $appBuilder->createDeactivationSection($entityName, $pkName, $activationKey);
        
        if($withStrash == 1)
        {
            $deleteSection = $appBuilder->createDeleteSection($entityName, $pkName, true, $entityNameTrash);
        }
        else
        {
            $deleteSection = $appBuilder->createDeleteSection($entityName, $pkName);
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
    
        ;

    $merged = (new AppSection(AppSection::SEPARATOR_NEW_LINE))
    ->add($usesSection)
    ->add($declarationSection)
    ->add($crudSection)
    ->add($guiSection)
    ;

    
    $fp = fopen(dirname(__DIR__)."/test.php", "w");
    fputs($fp, "<"."?php\r\n\r\n".$merged."\r\n\r\n");
    fclose($fp);
    
}
