<?php

namespace AppBuilder\Generator;

use AppBuilder\AppBuilder;
use AppBuilder\AppBuilderApproval;
use AppBuilder\AppFeatures;
use AppBuilder\AppField;
use AppBuilder\AppSecretObject;
use AppBuilder\AppSection;
use AppBuilder\Base\AppBuilderBase;
use AppBuilder\EntityApvInfo;
use AppBuilder\EntityInfo;
use MagicObject\Database\PicoDatabase;
use MagicObject\MagicObject;
use MagicObject\Request\InputPost;
use stdClass;

class ScriptGenerator
{
    /**
     * Create delete section without approval
     *
     * @param AppBuilder $appBuilder
     * @param MagicObject $entityMain
     * @param boolean $trashRequired
     * @param MagicObject $entityTrash
     * @return string
     */
    public function createDeleteWithoutApproval($appBuilder, $entityMain, $trashRequired, $entityTrash)
    {
        if($trashRequired)
        {
            return $appBuilder->createDeleteSection($entityMain, true, $entityTrash);
        }
        else
        {
            return $appBuilder->createDeleteSection($entityMain);
        }
    }

    /**
     * Get auth file
     *
     * @param AppSecretObject $appConf
     * @return string
     */
    public function getAuthFile($appConf)
    {
        $includeDir = trim($appConf->getBaseIncludeDirectory(), "/\\");
        if(!empty($includeDir)) 
        {
            $includeDir = '"/'.$includeDir.'/auth.php"';
        }
        else 
        {
            $includeDir = '"/auth.php"';
        }
        return $includeDir;
    }

    /**
     * Chek if field has reference data
     *
     * @param MagicObject $value
     * @return boolean
     */
    public function hasReferenceData($value)
    {
        return $value->getReferenceData() != null 
        && $value->getReferenceData()->getType() == 'entity' 
        && $value->getReferenceData()->getEntity() != null 
        && $value->getReferenceData()->getEntity()->getEntityName() != null;
    }

    /**
     * Add use from approval
     *
     * @param string[] $uses
     * @param AppSecretObject $appConf
     * @param boolean $approvalRequired
     * @param MagicObject $entity
     * @return string[]
     */
    public function addUseFromApproval($uses, $appConf, $approvalRequired, $entity)
    {
        if($approvalRequired) 
        {
            $entityApproval = $entity->getApprovalEntity();
            $entityApprovalName = $entityApproval->getEntityName();
            $uses[] = "use ".$appConf->getEntityBaseNamespace()."\\$entityApprovalName;";
        }
        return $uses;
    }

    /**
     * Add use from approval
     *
     * @param string[] $uses
     * @param AppSecretObject $appConf
     * @param boolean $trashRequired
     * @param MagicObject $entity
     * @return string[]
     */
    public function addUseFromTrash($uses, $appConf, $trashRequired, $entity)
    {
        if($trashRequired) 
        {
            $entityTrash = $entity->getTrashEntity();
            $entityTrashName = $entityTrash->getEntityName();
            $uses[] = "use ".$appConf->getEntityBaseNamespace()."\\$entityTrashName;";
        }
        return $uses;
    }

    /**
     * Add use from approval
     *
     * @param string[] $uses
     * @param array $referenceEntity
     * @param AppSecretObject $appConf
     * @return string[]
     */
    public function addUseFromReferenceData($uses, $appConf, $referenceEntity)
    {
        foreach($referenceEntity as $ref) 
        {
            $uses[] = "use ".$appConf->getEntityBaseNamespace()."\\$ref;";
        }
        return $uses;
    }

    /**
     * Generate
     *
     * @param PicoDatabase $database
     * @param MagicObject|InputPost $request
     * @param AppSecretObject $builderConfig
     * @param AppSecretObject $appConfig
     * @param EntityInfo $entityInfo
     * @param EntityApvInfo $entityApvInfo
     * @return void
     */
    public function generate($database, $request, $builderConfig, $appConfig, $entityInfo, $entityApvInfo)
    {
        $insertFields = array();
        $editFields = array();
        $detailFields = array();
        $listFields = array();
        $filterFields = array();
        $referenceEntity = array();
        foreach($request->getFields() as $value) {
            $field = new AppField($value);
            if($value->getIncludeInsert()) {
                $insertFields[$field->getFieldName()] = $field;
            }
            if($value->getIncludeEdit()) {
                $editFields[$field->getFieldName()] = $field;
            }
            if($value->getIncludeDetail()) {
                $detailFields[$field->getFieldName()] = $field;
            }
            if($value->getIncludeList()) {
                $listFields[$field->getFieldName()] = $field;
            }
            if($value->getFilterElementType() != "") {
                $filterFields[$field->getFieldName()] = $field;
            }
            if($this->hasReferenceData($value)){
                $referenceEntity[] = $value->getReferenceData()->getEntity()->getEntityName();
            }
        }
        
        $entity = $request->getEntity();
        
        $entityMain = $entity->getMainEntity();
        
        $entityApproval = new MagicObject();
        $entityTrash = new MagicObject();

        if($entity->getApprovalEntity() != null)
        {
            $entityApproval = $entity->getApprovalEntity();
        }
        if($entity->getTrashEntity() != null)
        {
            $entityTrash = $entity->getTrashEntity();
        }

        $entityMainName = $entityMain->getEntityName();
        $approvalRequired = AppBuilderBase::isTrue($entity->getApprovalRequired());
        $trashRequired = AppBuilderBase::isTrue($entity->getTrashRequired());
        
        $activationKey = $entityInfo->getActive();
        
        $appConf = new AppSecretObject($appConfig->getApplication());
        
        $uses = array();
        $uses[] = "// This script is generated automatically by AppBuilder";
        $uses[] = "// Visit https://github.com/Planetbiru/AppBuilder";
        $uses[] = "";
        $uses[] = "use MagicObject\\SetterGetter;";
        $uses[] = "use MagicObject\\Database\\PicoPage;";
        $uses[] = "use MagicObject\\Database\\PicoPageable;";
        $uses[] = "use MagicObject\\Database\\PicoPredicate;";
        $uses[] = "use MagicObject\\Database\\PicoSort;";
        $uses[] = "use MagicObject\\Database\\PicoSortable;";
        $uses[] = "use MagicObject\\Database\\PicoSpecification;";
        $uses[] = "use MagicObject\\Request\\PicoFilterConstant;";
        $uses[] = "use MagicObject\\Request\\InputGet;";
        $uses[] = "use MagicObject\\Request\\InputPost;";
        $uses[] = "use MagicObject\\Util\\AttrUtil;";
        $uses[] = "use AppBuilder\\Field;";
        $uses[] = "use AppBuilder\\PicoApproval;";
        $uses[] = "use AppBuilder\\UserAction;";
        $uses[] = "use AppBuilder\\AppInclude;";
        $uses[] = "use AppBuilder\\EntityLabel;";
        $uses[] = "use AppBuilder\\WaitingFor;";
        $uses[] = "use AppBuilder\\PicoTestUtil;";
        $uses[] = "use AppBuilder\\FormBuilder;";
        $uses[] = "use ".$appConf->getEntityBaseNamespace()."\\$entityMainName;";

        $uses = $this->addUseFromApproval($uses, $appConf, $approvalRequired, $entity);
        $uses = $this->addUseFromTrash($uses, $appConf, $approvalRequired, $entity);
        $uses = $this->addUseFromReferenceData($uses, $appConf, $referenceEntity);
        
        
        $uses[] = "";
        
        $includes = array();
        
        $includeDir = $this->getAuthFile($appConf);
        
        $includes[] = "require_once __DIR__ . $includeDir;";
        $includes[] = "";
        
        $usesSection = implode("\r\n", $uses);
        $includeSection = implode("\r\n", $includes);
        
        $declarationSection = implode("\r\n", array(AppBuilderBase::VAR."inputGet = new InputGet();", AppBuilderBase::VAR."inputPost = new InputPost();",""));
        
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
            $guiList = $appBuilder->createGuiList($entityMain, $listFields, $filterFields, $approvalRequired, $entityApproval); 
        }
        else
        {
            $appBuilder = new AppBuilder($builderConfig, $appConfig, $appFeatures, $entityInfo, $entityApvInfo);

            // CRUD
            $createSection = $appBuilder->createInsertSection($entityMain, $insertFields);
            $updateSection = $appBuilder->createUpdateSection($entityMain, $editFields);
            $activationSection = $appBuilder->createActivationSection($entityMain, $activationKey);
            $deactivationSection = $appBuilder->createDeactivationSection($entityMain, $activationKey);
            
            $deleteSection = $this->createDeleteWithoutApproval($appBuilder, $entityMain, $trashRequired, $entityTrash);

            $approvalSection = "";
            $rejectionSection = "";
            $guiInsert = $appBuilder->createGuiInsert($entityMain, $insertFields); 
            $guiUpdate = $appBuilder->createGuiUpdate($entityMain, $editFields); 
            $guiDetail = $appBuilder->createGuiDetail($entityMain, $detailFields); 
            $guiList = $appBuilder->createGuiList($entityMain, $listFields, $filterFields, $approvalRequired, $entityApproval); 
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
        ->add($guiList)
        ;

        $merged = (new AppSection(AppSection::SEPARATOR_NEW_LINE))
        ->add($usesSection)
        ->add($includeSection)
        ->add($declarationSection)
        ->add($crudSection)
        ->add($guiSection)
        ;

        $moduleFile = $request->getModuleFile();

        $baseDir = $appConf->getApplicationBaseDirectory();
        $this->prepareApplication($appConf, $baseDir);

        $path = $baseDir."/".$moduleFile;
        file_put_contents($path, "<"."?php\r\n\r\n".$merged."\r\n\r\n");

        
        $appBuilder->generateMainEntity($database, $builderConfig, $appConf, $entityMain, $entityInfo);
        $appBuilder->generateApprovalEntity($database, $builderConfig, $appConf, $entityMain, $entityInfo, $entityApproval);
        $appBuilder->generateTrashEntity($database, $builderConfig, $appConf, $entityMain, $entityInfo, $entityTrash);
    }

    public function prepareApplication($appConf, $baseDir)
    {
        if(!file_exists($baseDir)) 
        {    
            
            $this->prepareDir($baseDir);
            $this->prepareComposer($appConf);
                  
            $baseAppBuilder = $appConf->getEntityBaseDirectory()."";
            $this->prepareDir($baseAppBuilder);
            $arr = array( 
                'AppBuilder/Field.php',
                'AppBuilder/PicoApproval.php',
                'AppBuilder/UserAction.php',
                'AppBuilder/AppInclude.php',
                'AppBuilder/EntityLabel.php',
                'AppBuilder/WaitingFor.php',
                'AppBuilder/PicoTestUtil.php',            
                'AppBuilder/FormBuilder.php'            
            );
            foreach($arr as $file)
            {
                copy(dirname(dirname(__DIR__))."/".$file, $baseAppBuilder."/".$file);
            }
            
        }
   
        
    }
    public function prepareComposer($appConf)
    {
        $composer = new AppSecretObject($appConf->getComposer());
        $mo = new AppSecretObject($appConf->getMagicObject());
        $magicObjectVersion = $mo->getVersion();
        if(!empty($magicObjectVersion))
        {
            $magicObjectVersion = ":".$magicObjectVersion;
        }
        $this->prepareDir($appConf->getApplicationBaseDirectory()."/".$composer->getBaseDirectory());
        $targetDir = $appConf->getApplicationBaseDirectory()."/".$composer->getBaseDirectory()."";
        $targetPath = $appConf->getApplicationBaseDirectory()."/".$composer->getBaseDirectory()."/composer.phar";
        $success = copy(dirname(dirname(dirname(__DIR__)))."/composer.phar", $targetPath);
        if($success)
        {
            $cmd = "cd $targetDir"."&&"."php composer.phar require planetbiru/magic-object$magicObjectVersion";
            exec($cmd);     
            $this->updateComposer($appConf, $composer);
        }
    }
    public function prepareDir($baseDir)
    {
        if(!file_exists($baseDir)) {
            mkdir($baseDir, 0755, true);
        }
        
        

        
        
    }
    
    public function updateComposer($appConf, $composer)
    {
        $composerJsonFile = $appConf->getApplicationBaseDirectory()."/".$composer->getBaseDirectory()."/composer.json";
        
        
        $composerJson = json_decode(file_get_contents($composerJsonFile));
        if(!isset($composerJson->autoload))
        {
            $composerJson->autoload = new stdClass;
        }
        
        
        $psr0 = $composer->getPsr0();
        $psr4 = $composer->getPsr4();
        
        if($psr0)
        {
            if(!isset($composerJson->autoload->psr0))
            {
                $composerJson->autoload->{'psr-0'} = new stdClass;
            }
            $psr0BaseDirectory = $composer->getPsr0BaseDirectory();       
            foreach($psr0BaseDirectory as $dir)
            {
                $composerJson->autoload->{'psr-0'}->{$dir->getNamespace()."\\"} = $dir->getDirectory()."/";
            }
        }
        
        
        
        if($psr4)
        {
            if(!isset($composerJson->autoload->{'psr-4'}))
            {
                $composerJson->autoload->{'psr-4'} = new stdClass;
            }
            $psr0BaseDirectory = $composer->getPsr0BaseDirectory();       
            foreach($psr0BaseDirectory as $dir)
            {
                $composerJson->autoload->{'psr-4'}->{$dir->getNamespace()."\\"} = $dir->getDirectory()."/";
            }
        }
        
        $this->prepareDir($appConf->getApplicationBaseDirectory()."/".$composer->getBaseDirectory()."/classes/AppBuilder");
        
        
        if(!isset($composerJson->autoload->{'psr-0'}))
        {
            $composerJson->autoload->{'psr-0'} = new stdClass;
        }
        $composerJson->autoload->{'psr-0'}->{"AppBuilder\\"} = $dir->getDirectory()."/";

        
        file_put_contents($composerJsonFile, json_encode($composerJson, JSON_PRETTY_PRINT));

        $targetDir = $appConf->getApplicationBaseDirectory()."/".$composer->getBaseDirectory()."";
        $cmd = "cd $targetDir"."&&"."php composer.phar update";
        exec($cmd);

    }
}