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

require_once dirname(__DIR__) . "/inc.app/app.php";

$inputGet = new InputGet();
$inputPost = new InputPost();

$inputPost = new MagicObject(json_decode(file_get_contents("input.json")));
echo $inputPost;
if($inputPost->issetFields() && $inputPost->countableFields())
{
    $insertFields = array();
    $editFields = array();
    foreach($inputPost->getFields() as $index=>$value)
    {
        $field = new AppField($value);
        if($value['includeInsert'])
        {
            $insertFields[$field->getFieldName()] = $field;
        }
        if($value['includeEdit'])
        {
            $editFields[$field->getFieldName()] = $field;
        }
    }
    
    $entityName = $inputPost->getEntity();
    $requireApproval = $inputPost->getRequireApproval();
    $entityApprovalName = $inputPost->getEntityApproval();
    
    $activationKey = $entityInfo->getActive();
    $pkName = $inputPost->getPrimaryKeyName();
    $withStrash = $inputPost->getWithTrash();
    $entityTrashName = $inputPost->getEntityTrash();
    $pkApprovalName = $inputPost->getPrimaryKeyApprovalName();
    
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
    $uses[] = "use ".$appConf->getEntityBaseNamespace()."\\$entityName;";
    $uses[] = "use ".$appConf->getEntityBaseNamespace()."\\$entityApprovalName;";
    $uses[] = "use ".$appConf->getEntityBaseNamespace()."\\$entityTrashName;";

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
    if($requireApproval == 1)
    {
        $appBuilderApv = new AppBuilderApproval($database, $appConfig, $entityInfo, $entityApvInfo);

        $createSection = $appBuilderApv->createInsertApprovalSection($entityName, $insertFields, $pkName, $entityApprovalName);
        $updateSection = $appBuilderApv->createUpdateApprovalSection($entityName, $editFields, $pkName, $entityApprovalName, $pkApprovalName);
        $activationSection = $appBuilderApv->createActivationApprovalSection($entityName, $pkName);
        $deactivationSection = $appBuilderApv->createDeactivationApprovalSection($entityName, $pkName);     
        $deleteSection = $appBuilderApv->createDeleteApprovalSection($entityName, $pkName);
        $approvalSection = $appBuilderApv->createApprovalSection($entityName, $pkName, $editFields, $entityApprovalName, $entityTrashName);
        $rejectionSection = $appBuilderApv->createRejectionSection($entityName, $pkName, $entityApprovalName);  
        
        $guiInsert = $appBuilderApv->createGuiInsert($entityName, $insertFields, $pkName, $entityApprovalName);
        
        
    }
    else
    {
        $appBuilder = new AppBuilder($database, $appConfig, $entityInfo, $entityApvInfo);

        $createSection = $appBuilder->createInsertSection($entityName, $insertFields);
        $updateSection = $appBuilder->createUpdateSection($entityName, $editFields);
        $activationSection = $appBuilder->createActivationSection($entityName, $pkName, $activationKey);
        $deactivationSection = $appBuilder->createDeactivationSection($entityName, $pkName, $activationKey);
        
        if($withStrash == 1)
        {
            $deleteSection = $appBuilder->createDeleteSection($entityName, $pkName, true, $entityTrashName);
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
