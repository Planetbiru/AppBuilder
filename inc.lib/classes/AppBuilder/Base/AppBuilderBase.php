<?php

namespace AppBuilder\Base;

use AppBuilder\AppEntityGenerator;
use AppBuilder\AppFeatures;
use AppBuilder\AppField;
use AppBuilder\AppSecretObject;
use AppBuilder\ElementType;
use AppBuilder\EntityApvInfo;
use AppBuilder\EntityInfo;
use DOMDocument;
use DOMElement;
use MagicObject\Database\PicoDatabase;
use MagicObject\MagicObject;
use MagicObject\SecretObject;
use MagicObject\Util\PicoStringUtil;

class AppBuilderBase //NOSONAR
{
    const TAB1 = "\t";
    const TAB2 = "\t\t";
    const TAB3 = "\t\t\t";
    const TAB4 = "\t\t\t\t";
    const NEW_LINE = "\r\n";
    const NEW_LINE_R = "\r";
    const NEW_LINE_N = "\n";
    const VAR = "$";
    const CALL_INSERT_END = "->insert();";
    const CALL_UPDATE_END = "->update();";
    const CALL_DELETE_END = "->delete();";
    const CALL_SET = "->set";
    const CALL_GET = "->get";
    
    const PHP_OPEN_TAG = '<'.'?'.'php ';
    const PHP_CLOSE_TAG = '?'.'>';
    
    const STYLE_NATIVE = 'native';
    const STYLE_SETTER_GETTER = 'setter-getter';
    const ECHO = 'echo ';

    const WRAPPER_INSERT = "insert";
    const WRAPPER_UPDATE = "update";
    const WRAPPER_DETAIL = "detail";
    const WRAPPER_LIST = "list";

    const APP_CONFIG = "appConfig";
    const CURLY_BRACKET_OPEN = "{";
    const CURLY_BRACKET_CLOSE = "}";

    /**
     * Set and get value style
     *
     * @var string
     */
    protected $style = self::STYLE_NATIVE;
    
    /**
     * Config base directory
     *
     * @var string
     */
    protected $configBaseDirectory = "";

    protected $skipedAutoSetter = array();

    /**
     * Entity info
     *
     * @var EntityInfo
     */
    protected $entityInfo;

    /**
     * Entity approval info
     *
     * @var EntityApvInfo
     */
    protected $entityApvInfo;

    /**
     * AppBuilder config
     *
     * @var SecretObject
     */
    protected $appBuilderConfig;
    /**
     * Application config
     *
     * @var SecretObject
     */
    protected $appConfig;

    /**
     * Current action
     *
     * @var SecretObject
     */
    protected $currentAction;
    
    /**
     * App feature
     *
     * @var AppFeatures
     */
    protected $appFeatures;
    
    /**
     * All field
     *
     * @var AppField[]
     */
    protected $allField;

    /**
     * Constructor
     *
     * @param SecretObject $appBuilderConfig
     * @param SecretObject $appConfig
     * @param AppFeatures $appFeatures
     * @param EntityInfo $entityInfo
     * @param EntityApvInfo $entityApvInfo
     * @param AppField[] $allField
     */
    public function __construct($appBuilderConfig, $appConfig, $appFeatures, $entityInfo, $entityApvInfo, $allField)
    {
        $this->appBuilderConfig = $appBuilderConfig;
        $this->appConfig = $appConfig;
        $this->appFeatures = $appFeatures;
        $this->allField = $allField;
        
        $this->currentAction = new AppSecretObject($appBuilderConfig->getCurrentAction());
        $this->configBaseDirectory = $appBuilderConfig->getConfigBaseDirectory();
        $this->entityInfo = $entityInfo;
        $this->entityApvInfo = $entityApvInfo;
        $this->skipedAutoSetter = array(
            $entityInfo->getDraft(),
            $entityInfo->getAdminCreate(),
            $entityInfo->getAdminEdit(),
            $entityInfo->getAdminAskEdit(),
            $entityInfo->getIpCreate(),
            $entityInfo->getIpEdit(),
            $entityInfo->getIpAskEdit(),
            $entityInfo->getTimeCreate(),
            $entityInfo->getTimeEdit(),
            $entityInfo->getTimeAskEdit(),
            $entityInfo->getWaitingFor(),
            $entityInfo->getApprovalId()
        );
    }
    
    public function getInputFilter($fieldName)
    {
        foreach($this->allField as $field)
        {
            if($field->getFieldName() == $fieldName)
            {
                return $field->getInputFilter();
            }
        }
        return null;
    }
    
    /**
     * Load application config
     *
     * @param string $appName
     * @return MagicObject
     */
    public function loadApplicationConfig($appName)
    {
        $config = new MagicObject();
        $appConfigPath = $this->configBaseDirectory . "/" . $appName . ".yml";
        $config->loadYamlFile($appConfigPath);
        return $config;
    }

    /**
     * Get config base directory
     *
     * @return  string
     */
    public function getConfigBaseDirectory()
    {
        return $this->configBaseDirectory;
    }

    /**
     * Set config base directory
     *
     * @param  string  $configBaseDirectory  Config base directory
     *
     * @return  self
     */
    public function setConfigBaseDirectory($configBaseDirectory)
    {
        $this->configBaseDirectory = $configBaseDirectory;

        return $this;
    }

    /**
     * Create constructor
     *
     * @param string $objectName
     * @param string $entityName
     * @return string
     */
    protected function createConstructor($objectName, $entityName, $dataToLoad = null)
    {
        if ($dataToLoad == null) {
            $dataToLoad = "null";
        } else {
            $dataToLoad = self::VAR . $dataToLoad;
        }
        return self::VAR . $objectName . " = new $entityName($dataToLoad, " . self::VAR . $this->appBuilderConfig->getGlobalVariableDatabase() . ");";
    }

    /**
     * Create setter for object
     *
     * @param string $objectName
     * @param string $fieldName
     * @param string $fieldFilter
     * @return string
     */
    protected function createSetter($objectName, $fieldName, $fieldFilter, $primaryKeyName = null, $updatePk = false)
    {
        if (in_array($fieldName, $this->skipedAutoSetter)) {
            return null;
        }
        if ($this->style = self::STYLE_SETTER_GETTER) {
            if($primaryKeyName != null && $primaryKeyName == $fieldName && $updatePk)
            {
                $methodSource = PicoStringUtil::upperCamelize("app_builder_new_pk").PicoStringUtil::upperCamelize($fieldName);
            }
            else
            {
                $methodSource = PicoStringUtil::upperCamelize($fieldName);
            }
            $methodTarget = PicoStringUtil::upperCamelize($fieldName);
            return self::TAB1 . self::VAR . $objectName . self::CALL_SET . $methodTarget . "(" . self::VAR . "inputPost".self::CALL_GET.$methodSource . "(PicoFilterConstant::" . $fieldFilter . "));";
        } else {
            return self::TAB1 . self::VAR . $objectName . self::CALL_SET."('" . $fieldName . "', " . self::VAR . "inputPost".self::CALL_GET."('" . $fieldName . "', PicoFilterConstant::" . $fieldFilter . "));";
        }
    }




    /**
     * Get entity info
     *
     * @return  EntityInfo
     */
    public function getentityInfo()
    {
        return $this->entityInfo;
    }

    /**
     * Set entity info
     *
     * @param  EntityInfo  $entityInfo  Entity info
     *
     * @return  self
     */
    public function setentityInfo($entityInfo)
    {
        $this->entityInfo = $entityInfo;

        return $this;
    }

    /**
     * Load or create application config
     *
     * @param string $appId
     * @param string $appBaseConfigPath
     * @param string $configTemplatePath
     * @return AppSecretObject
     */
    public static function loadOrCreateConfig($appId, $appBaseConfigPath, $configTemplatePath)
    {
        $appConfig = new AppSecretObject();
        if ($appId != null) {
            $appConfigPath = $appBaseConfigPath . "/" . $appId . "/default.yml";
            $appConfigDir = $appBaseConfigPath . "/" . $appId;
            if (file_exists($appConfigPath)) {
                $appConfig->loadYamlFile($appConfigPath, false, true, true);
            } else {
                $appConfig->loadYamlFile($configTemplatePath, false, true, true);
                if (!file_exists($appConfigDir)) {
                    mkdir($appConfigDir, 0755, true);
                }
                copy($configTemplatePath, $appConfigPath);
            }
        }
        return $appConfig;
    }

    /**
     * Load or create application config
     *
     * @param string $appId
     * @param string $appBaseConfigPath
     * @param SecretObject $appConfig
     */
    public static function updateConfig($appId, $appBaseConfigPath, $appConfig)
    {
        if ($appId != null) {
            $appConfigPath = $appBaseConfigPath . "/" . $appId . "/default.yml";
            $appConfigDir = $appBaseConfigPath . "/" . $appId;

            if (!file_exists($appConfigDir)) {
                mkdir($appConfigDir, 0755, true);
            }
            file_put_contents($appConfigPath, $appConfig->dumpYaml());
        }
    }

    /**
     * Get appBuilder config
     *
     * @return  SecretObject
     */
    public function getAppBuilderConfig()
    {
        return $this->appBuilderConfig;
    }


    /**
     * Get current action
     *
     * @return  SecretObject
     */ 
    public function getCurrentAction()
    {
        return $this->currentAction;
    }
    
    /**
     * Create element form
     *
     * @param DOMDocument $dom
     * @param string $name
     * @return DOMElement
     */
    private function createElementForm($dom, $name)
    {
        $form = $dom->createElement('form');
        $form->setAttribute('name', $name);
        $form->setAttribute('id', $name);
        $form->setAttribute('action', '');
        $form->setAttribute('method', 'post');
        return $form; 
    }
    
    /**
     * Create element table responsive
     *
     * @param DOMDocument $dom
     * @return DOMElement
     */
    private function createElementTableResponsive($dom)
    {
        $table = $dom->createElement('table');
        $table->setAttribute('class', 'responsive responsive-two-cols');
        $table->setAttribute('border', '0');
        $table->setAttribute('cellpadding', '0');
        $table->setAttribute('cellspacing', '0');
        $table->setAttribute('width', '100%');
        return $table;
    }
    
    /**
     * Create element submit button
     *
     * @param DOMDocument $dom
     * @return DOMElement
     */
    private function createSubmitButton($dom, $value, $name = null, $id = null)
    {
        // <input type="submit" class="btn btn-success" id="save" name="button_save" value="Simpan">
        $input = $dom->createElement('input');
        $input->setAttribute('type', 'submit');
        $input->setAttribute('class', 'btn btn-success');
        if($name != null)
        {
            $input->setAttribute('name', $name);
        }
        if($id != null)
        {
            $input->setAttribute('id', $id);
        }
        $input->setAttribute('value', $value);
        return $input;
    }
    
    /**
     * Create element cancel button
     *
     * @param DOMDocument $dom
     * @return DOMElement
     */
    private function createCancelButton($dom, $value, $name = null, $id = null, $onclickUrlVariable = null)
    {
        $input = $dom->createElement('input');
        $input->setAttribute('type', 'button');
        $input->setAttribute('class', 'btn btn-primary');
        if($name != null)
        {
            $input->setAttribute('name', $name);
        }
        if($id != null)
        {
            $input->setAttribute('id', $id);
        }
        $input->setAttribute('value', $value);
        if($onclickUrlVariable != null)
        {
            $input->setAttribute('onclick', "window.location='".self::PHP_OPEN_TAG.self::ECHO.self::VAR.$onclickUrlVariable.";".self::PHP_CLOSE_TAG."';");
        }
        return $input;
    }
    
    /**
     * Get text
     *
     * @param string $id
     * @return string
     */
    private function getTextOfLanguage($id)
    {
        if($this->style == self::STYLE_SETTER_GETTER)
        {
            $param = PicoStringUtil::upperCamelize($id);
            return self::PHP_OPEN_TAG.self::ECHO.self::VAR."appLanguage->get$param"."(); ".self::PHP_CLOSE_TAG;
        }
        else
        {
            return self::PHP_OPEN_TAG.self::ECHO.self::VAR."appLanguage->get('".$id."'); ".self::PHP_CLOSE_TAG;
        }
    }

    /**
     * Fix table tags
     *
     * @param string $html
     * @return string
     */
    private function fixTable($html)
    {
        $html = str_replace('<div/>', '<div></div>', $html);
        $html = str_replace('<td/>', '<td></td>', $html);
        $html = str_replace('<th/>', '<th></th>', $html);
        $html = str_replace('<tr/>', '<tr></tr>', $html);
        $html = str_replace('<thead/>', '<thead></thead>', $html);
        $html = str_replace('<tbody/>', '<tbody></tbody>', $html);
        $html = str_replace('<table/>', '<table></table>', $html);
        return $html;
    }

    /**
     * Fix PHP code
     *
     * @param string $html
     * @return string
     */
    private function fixPhpCode($html)
    {
        return str_replace(
            array('&lt;?php', '?&gt;', '-&gt;', '=&gt;', '&gt;', '&lt;', '&quot;', '&amp;'), 
            array('<'.'?'.'php', '?'.'>', '->', '=>', '>', '<', '"', '&'), 
            $html
        );
    }
    
    /**
     * Create GUI INSERT section without approval
     *
     * @param AppField[] $appFields
     * @param MagicObject $mainEntity
     * @param boolean $approvalRequired
     * @param MagicObject $approvalEntity
     * @return string
     */
    public function createGuiInsert($mainEntity, $insertFields, $approvalRequired = false, $approvalEntity = null)
    {
        $entityName = $mainEntity->getEntityName();
        $primaryKeyName =  $mainEntity->getPrimaryKey();

        $objectName = lcfirst($entityName);
        $dom = new DOMDocument();
        
        $form = $this->createElementForm($dom, 'createform');
        
        $table1 = $this->createInsertFormTable($dom, $mainEntity, $objectName, $insertFields, $primaryKeyName);


        $table2 = $this->createButtonContainerTableCreate($dom, "save-create", "save-create", 'UserAction::CREATE');

        $form->appendChild($table1);
        $form->appendChild($table2);
        
        $dom->appendChild($form);
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;

        $xml = $dom->saveXML();
        $html = $this->xmlToHtml($xml);     
        $html = $this->fixTable($html);
        $html = $this->fixPhpCode($html);
        
        $html = trim($html, self::NEW_LINE);
        $html = $this->addTab($html, 2);
        $html = $this->addIndent($html, 2);
        $html = $this->addWrapper($html, self::WRAPPER_INSERT);
        
        return "if(".self::VAR."inputGet->getUserAction() == UserAction::CREATE)\r\n"
        .self::CURLY_BRACKET_OPEN.self::NEW_LINE
        .$this->getIncludeHeader().self::NEW_LINE
        .$this->constructEntityLabel($entityName).self::NEW_LINE
        .self::PHP_CLOSE_TAG.self::NEW_LINE.$html.self::NEW_LINE.self::PHP_OPEN_TAG.self::NEW_LINE
        .$this->getIncludeFooter().self::NEW_LINE
        .self::CURLY_BRACKET_CLOSE;
    }
    
    /**
     * Create GUI INSERT section without approval
     *
     * @param AppField[] $appFields
     * @param MagicObject $mainEntity
     * @param boolean $approvalRequired
     * @param MagicObject $approvalEntity
     * @return string
     */
    public function createGuiUpdate($mainEntity, $insertFields, $approvalRequired = false, $approvalEntity = null)
    {
        
        $entityName = $mainEntity->getEntityName();
        $primaryKeyName =  $mainEntity->getPrimaryKey();
        $upperPkName = PicoStringUtil::upperCamelize($primaryKeyName);

        $objectName = lcfirst($entityName);
        $dom = new DOMDocument();
        
        $form = $this->createElementForm($dom, 'updateform');
        
        $table1 = $this->createUpdateFormTable($dom, $mainEntity, $objectName, $insertFields, $primaryKeyName);

        

        $table2 = $this->createButtonContainerTableUpdate($dom, "save-update", "save-update", 'UserAction::UPDATE', $objectName, $primaryKeyName);

        $form->appendChild($table1);
        $form->appendChild($table2);
        
        $dom->appendChild($form);
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;

        $xml = $dom->saveXML();

        $html = $this->xmlToHtml($xml);
        $html = $this->fixTable($html);
        $html = $this->fixPhpCode($html);
        
        $html = trim($html, self::NEW_LINE);
        
        $html = $this->addTab($html, 2);
        $html = $this->addIndent($html, 2);
        $html = $this->addWrapper($html, self::WRAPPER_UPDATE);
        
        $getData = array();
        $getData[] = self::TAB1.$this->createConstructor($objectName, $entityName);
        $getData[] = self::TAB1."try{";
        $getData[] = self::TAB1.self::TAB1.self::VAR.$objectName."->findOneBy".$upperPkName."(".self::VAR."inputGet".self::CALL_GET.$upperPkName."());";
        $getData[] = self::TAB1.self::TAB1."if(".self::VAR.$objectName."->hasValue".$upperPkName."())";
        $getData[] = self::TAB1.self::TAB1.self::CURLY_BRACKET_OPEN;
        $getData[] = $this->getIncludeHeader();
        $getData[] = $this->constructEntityLabel($entityName);
        $getData[] = self::PHP_CLOSE_TAG.self::NEW_LINE.$html.self::NEW_LINE.self::PHP_OPEN_TAG;
        $getData[] = self::TAB1.self::TAB1.self::CURLY_BRACKET_CLOSE;
        $getData[] = self::TAB1.self::TAB1."else";
        $getData[] = self::TAB1.self::TAB1.self::CURLY_BRACKET_OPEN;
        $getData[] = self::TAB1.self::TAB1.self::TAB1."// Do somtething here when data is not found";
        $getData[] = self::TAB1.self::TAB1.self::TAB1.'?>';
        $getData[] = self::TAB1.self::TAB1.self::TAB1.'<div class="alert alert-warning"><?php echo $appLanguage->getMessageDataNotFound();?></div>';
        $getData[] = self::TAB1.self::TAB1.self::TAB1.'<?php';
        $getData[] = self::TAB1.self::TAB1.self::CURLY_BRACKET_CLOSE;
        $getData[] = $this->getIncludeFooter();
        $getData[] = self::TAB1.self::CURLY_BRACKET_CLOSE;
        $getData[] = self::TAB1."catch(Exception ".self::VAR."e)";
        $getData[] = self::TAB1.self::CURLY_BRACKET_OPEN;
        $getData[] = $this->getIncludeHeader();
        $getData[] = self::TAB1.self::TAB1."// Do somtething here when exception";
        $getData[] = self::TAB1.self::TAB1.'?>';
        $getData[] = self::TAB1.self::TAB1.'<div class="alert alert-danger"><?php echo $e->getMessage();?></div>';
        $getData[] = self::TAB1.self::TAB1.'<?php';
        $getData[] = $this->getIncludeFooter();
        $getData[] = self::TAB1.self::CURLY_BRACKET_CLOSE.self::NEW_LINE;

        return "if(".self::VAR."inputGet->getUserAction() == UserAction::UPDATE)\r\n"
        ."{\r\n"
        .implode(self::NEW_LINE, $getData)
        .self::CURLY_BRACKET_CLOSE;
    }
    
    /**
     * Create GUI DETAIL section without approval
     *
     * @param MagicObject $mainEntity
     * @param AppField[] $appFields
     * @param boolean $approvalRequired
     * @param MagicObject $approvalEntity
     * @return string
     */
    public function createGuiDetail($mainEntity, $appFields, $approvalRequired = false, $approvalEntity = null)
    {
        if($approvalRequired)
        {
            return $this->createGuiDetailWithApproval($mainEntity, $appFields, $approvalRequired, $approvalEntity);
        }
        else
        {
            return $this->createGuiDetailWithoutApproval($mainEntity, $appFields);
        }
    }
    /**
     * Create GUI DETAIL section with approval
     *
     * @param MagicObject $mainEntity
     * @param AppField[] $appFields
     * @param boolean $approvalRequired
     * @param MagicObject $approvalEntity
     * @return string
     */
    public function createGuiDetailWithApproval($mainEntity, $appFields, $approvalRequired = false, $approvalEntity = null)
    {
        $entityName = $mainEntity->getEntityName();
        $entityApprovalName = $approvalEntity->getEntityName();
        $objectApprovalName = PicoStringUtil::camelize($entityApprovalName);
        $primaryKeyName =  $mainEntity->getPrimaryKey();
        $upperPkName = PicoStringUtil::upperCamelize($primaryKeyName);

        $objectName = lcfirst($entityName);
        
        $htmlDetail = $this->createTableDetail($mainEntity, $objectName, $appFields, $primaryKeyName);

        $htmlDetailCompare = $this->createTableDetailCompare($mainEntity, $objectName, $appFields, $primaryKeyName, $approvalEntity, $objectApprovalName);

        $getData = array();
        $getData[] = self::TAB1.$this->createConstructor($objectName, $entityName);
        $getData[] = self::TAB1."try{";
        $getData[] = self::TAB1.self::TAB1.self::VAR.$objectName."->findOneBy".$upperPkName."(".self::VAR."inputGet".self::CALL_GET.$upperPkName."());";
        $getData[] = self::TAB1.self::TAB1."if(".self::VAR.$objectName."->hasValue".$upperPkName."())";
        $getData[] = self::TAB1.self::TAB1.self::CURLY_BRACKET_OPEN;

        $getData[] = self::TAB1.self::TAB1.self::TAB1."if(".self::VAR.$objectName."->notNullApprovalId())";
        $getData[] = self::TAB1.self::TAB1.self::TAB1.self::CURLY_BRACKET_OPEN;
        $getData[] = self::TAB1.self::TAB1.self::TAB1.self::TAB1.$this->createConstructor($objectApprovalName, $entityApprovalName);
        $getData[] = self::TAB1.self::TAB1.self::TAB1.self::TAB1."try";
        $getData[] = self::TAB1.self::TAB1.self::TAB1.self::TAB1.self::CURLY_BRACKET_OPEN;
        $getData[] = self::TAB1.self::TAB1.self::TAB1.self::TAB1.self::TAB1.self::VAR.$objectApprovalName."->find(".self::VAR.$objectName.self::CALL_GET."ApprovalId());";
        $getData[] = self::TAB1.self::TAB1.self::TAB1.self::TAB1.self::CURLY_BRACKET_CLOSE;
        $getData[] = self::TAB1.self::TAB1.self::TAB1.self::TAB1."catch(Exception ".self::VAR."e)";
        $getData[] = self::TAB1.self::TAB1.self::TAB1.self::TAB1.self::CURLY_BRACKET_OPEN;
        $getData[] = self::TAB1.self::TAB1.self::TAB1.self::TAB1.self::TAB1."// do something here";
        $getData[] = self::TAB1.self::TAB1.self::TAB1.self::TAB1.self::CURLY_BRACKET_CLOSE;

        $getData[] = $this->getIncludeHeader();
        $getData[] = $this->constructEntityLabel($entityName);
        $getData[] = self::PHP_CLOSE_TAG.self::NEW_LINE.$htmlDetailCompare.self::NEW_LINE.self::PHP_OPEN_TAG;
        $getData[] = $this->getIncludeFooter();

        $getData[] = self::TAB1.self::TAB1.self::TAB1.self::CURLY_BRACKET_CLOSE;
        $getData[] = self::TAB1.self::TAB1.self::TAB1."else";
        $getData[] = self::TAB1.self::TAB1.self::TAB1.self::CURLY_BRACKET_OPEN;

        $getData[] = $this->getIncludeHeader();
        $getData[] = $this->constructEntityLabel($entityName);
        $getData[] = self::PHP_CLOSE_TAG.self::NEW_LINE.$htmlDetail.self::NEW_LINE.self::PHP_OPEN_TAG;
        $getData[] = $this->getIncludeFooter();

        $getData[] = self::TAB1.self::TAB1.self::TAB1.self::CURLY_BRACKET_CLOSE;
            

        $getData[] = self::TAB1.self::TAB1.self::CURLY_BRACKET_CLOSE;
        $getData[] = self::TAB1.self::TAB1."else";
        $getData[] = self::TAB1.self::TAB1.self::CURLY_BRACKET_OPEN;
        $getData[] = $this->getIncludeHeader();
        $getData[] = self::TAB1.self::TAB1.self::TAB1."// Do somtething here when data is not found";
        $getData[] = self::TAB1.self::TAB1.self::TAB1.'?>';
        $getData[] = self::TAB1.self::TAB1.self::TAB1.'<div class="alert alert-warning"><?php echo $appLanguage->getMessageDataNotFound();?></div>';
        $getData[] = self::TAB1.self::TAB1.self::TAB1.'<?php';
        $getData[] = $this->getIncludeFooter();
        $getData[] = self::TAB1.self::TAB1.self::CURLY_BRACKET_CLOSE;
        $getData[] = self::TAB1.self::CURLY_BRACKET_CLOSE;
        $getData[] = self::TAB1."catch(Exception ".self::VAR."e)";
        $getData[] = self::TAB1.self::CURLY_BRACKET_OPEN;
        $getData[] = $this->getIncludeHeader();
        $getData[] = self::TAB1.self::TAB1."// Do somtething here when exception";
        $getData[] = self::TAB1.self::TAB1.'?>';
        $getData[] = self::TAB1.self::TAB1.'<div class="alert alert-danger"><?php echo $e->getMessage();?></div>';
        $getData[] = self::TAB1.self::TAB1.'<?php';
        $getData[] = $this->getIncludeFooter();
        $getData[] = self::TAB1.self::CURLY_BRACKET_CLOSE.self::NEW_LINE;

        return "if(".self::VAR."inputGet->getUserAction() == UserAction::DETAIL)\r\n"
        ."{\r\n"
        .implode(self::NEW_LINE, $getData)
        .self::CURLY_BRACKET_CLOSE;
    }
    
    /**
     * Create GUI DETAIL section with approval
     *
     * @param MagicObject $mainEntity
     * @param AppField[] $appFields
     * @return string
     */
    public function createGuiDetailWithoutApproval($mainEntity, $appFields)
    {
        $entityName = $mainEntity->getEntityName();
        $primaryKeyName =  $mainEntity->getPrimaryKey();
        $upperPkName = PicoStringUtil::upperCamelize($primaryKeyName);

        $objectName = lcfirst($entityName);
        
        $htmlDetail = $this->createTableDetail($mainEntity, $objectName, $appFields, $primaryKeyName);


        $getData = array();
        $getData[] = self::TAB1.$this->createConstructor($objectName, $entityName);
        $getData[] = self::TAB1."try{";
        $getData[] = self::TAB1.self::TAB1.self::VAR.$objectName."->findOneBy".$upperPkName."(".self::VAR."inputGet".self::CALL_GET.$upperPkName."());";
        $getData[] = self::TAB1.self::TAB1."if(".self::VAR.$objectName."->hasValue".$upperPkName."())";
        $getData[] = self::TAB1.self::TAB1.self::CURLY_BRACKET_OPEN;



        $getData[] = $this->getIncludeHeader();
        $getData[] = $this->constructEntityLabel($entityName);
        $getData[] = self::PHP_CLOSE_TAG.self::NEW_LINE.$htmlDetail.self::NEW_LINE.self::PHP_OPEN_TAG;
        $getData[] = $this->getIncludeFooter();

            

        $getData[] = self::TAB1.self::TAB1.self::CURLY_BRACKET_CLOSE;
        $getData[] = self::TAB1.self::TAB1."else";
        $getData[] = self::TAB1.self::TAB1.self::CURLY_BRACKET_OPEN;
        $getData[] = self::TAB1.self::TAB1.self::TAB1."// Do somtething here when data is not found";
        $getData[] = self::TAB1.self::TAB1.self::TAB1.'?>';
        $getData[] = self::TAB1.self::TAB1.self::TAB1.'<div class="alert alert-warning"><?php echo $appLanguage->getMessageDataNotFound();?></div>';
        $getData[] = self::TAB1.self::TAB1.self::TAB1.'<?php';
        $getData[] = self::TAB1.self::TAB1.self::CURLY_BRACKET_CLOSE;
        $getData[] = self::TAB1.self::CURLY_BRACKET_CLOSE;
        $getData[] = self::TAB1."catch(Exception ".self::VAR."e)";
        $getData[] = self::TAB1.self::CURLY_BRACKET_OPEN;
        $getData[] = $this->getIncludeHeader();
        $getData[] = self::TAB1.self::TAB1."// Do somtething here when exception";
        $getData[] = self::TAB1.self::TAB1.'?>';
        $getData[] = self::TAB1.self::TAB1.'<div class="alert alert-danger"><?php echo $e->getMessage();?></div>';
        $getData[] = self::TAB1.self::TAB1.'<?php';
        $getData[] = $this->getIncludeFooter();
        $getData[] = self::TAB1.self::CURLY_BRACKET_CLOSE.self::NEW_LINE;

        return "if(".self::VAR."inputGet->getUserAction() == UserAction::DETAIL)\r\n"
        ."{\r\n"
        .implode(self::NEW_LINE, $getData)
        .self::CURLY_BRACKET_CLOSE;
    }

    /**
     * Undocumented function
     *
     * @param MagicObject $mainEntity
     * @param string $objectName
     * @param AppField[] $appFields
     * @param string $primaryKeyName
     * @return string
     */
    public function createTableDetail($mainEntity, $objectName, $appFields, $primaryKeyName)
    {
        $dom = new DOMDocument();
        
        $formDetail = $this->createElementForm($dom, 'detailform');
        $tableDetail1 = $this->createDetailTable($dom, $mainEntity, $objectName, $appFields, $primaryKeyName);
        $tableDetail2 = $this->createButtonContainerTableDetail($dom, "save-update", "save-update", 'UserAction::DETAIL', $objectName, $primaryKeyName);

        $formDetail->appendChild($tableDetail1);
        $formDetail->appendChild($tableDetail2);
        
        $dom->appendChild($formDetail);
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;

        $xml = $dom->saveXML();

        $htmlDetail = $this->xmlToHtml($xml);
        $htmlDetail = $this->fixTable($htmlDetail);
        $htmlDetail = $this->fixPhpCode($htmlDetail);
        $htmlDetail = trim($htmlDetail, self::NEW_LINE);
        
        $htmlDetail = $this->addTab($htmlDetail, 2);
        $htmlDetail = $this->addIndent($htmlDetail, 2);
        $htmlDetail = $this->addWrapper($htmlDetail, self::WRAPPER_DETAIL);

        return $htmlDetail;
    }

    /**
     * Undocumented function
     *
     * @param MagicObject $mainEntity
     * @param string $objectName
     * @param AppField[] $appFields
     * @param string $primaryKeyName
     * @return string
     */
    public function createTableDetailCompare($mainEntity, $objectName, $appFields, $primaryKeyName, $approvalEntity, $objectApprovalName)
    {
        $dom = new DOMDocument();
        
        $formDetail = $this->createElementForm($dom, 'detailform');
        
        $upperWaitingFor = PicoStringUtil::upperCamelize($this->entityInfo->getWaitingFor());
        
        $div = $dom->createElement('div');
        $div->setAttribute('class', 'alert alert-info');
            
        $messagePhp = '
<?php
if($'.$objectName.'->get'.$upperWaitingFor.'() == WaitingFor::CREATE)
{
    echo $appLanguage->getMessageWaitingForCreate();
}
else if($'.$objectName.'->get'.$upperWaitingFor.'() == WaitingFor::UPDATE)
{
    echo $appLanguage->getMessageWaitingForUpdate();
}
else if($'.$objectName.'->get'.$upperWaitingFor.'() == WaitingFor::ACTIVATE)
{
    echo $appLanguage->getMessageWaitingForActivate();
}
else if($'.$objectName.'->get'.$upperWaitingFor.'() == WaitingFor::DEACTIVATE)
{
    echo $appLanguage->getMessageWaitingForDeactivate();
}
else if($'.$objectName.'->get'.$upperWaitingFor.'() == WaitingFor::DELETE)
{
    echo $appLanguage->getMessageWaitingForDelete();
}
?>
';
        $messagePhp = str_replace("\r\n", "\n", $messagePhp);
        $messagePhp = $this->addIndent($messagePhp, 1);
        $messagePhp = str_replace("\r\n", "\n", $messagePhp);
        
        $message = $dom->createTextNode($messagePhp);

        $div->appendChild($message);
        
        $formDetail->appendChild($div);
        
        $tableDetail1 = $this->createDetailTableCompare($dom, $mainEntity, $objectName, $appFields, $primaryKeyName, $approvalEntity, $objectApprovalName);
        $tableDetail2 = $this->createButtonContainerTableApproval($dom, "save-update", "save-update", '$inputGet->getNextAction()', $objectName, $primaryKeyName);

        $formDetail->appendChild($tableDetail1);
        $formDetail->appendChild($tableDetail2);
        
        $dom->appendChild($formDetail);
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;

        $xml = $dom->saveXML();

        $htmlDetail = $this->xmlToHtml($xml);
        $htmlDetail = $this->fixTable($htmlDetail);
        $htmlDetail = $this->fixPhpCode($htmlDetail);
        $htmlDetail = trim($htmlDetail, self::NEW_LINE);
        
        $htmlDetail = $this->addTab($htmlDetail, 2);
        $htmlDetail = $this->addIndent($htmlDetail, 2);
        $htmlDetail = $this->addWrapper($htmlDetail, self::WRAPPER_DETAIL);

        return $htmlDetail;
    }

    /**
     * Create GUI LIST section 
     *
     * @param MagicObject $mainEntity
     * @param AppField[] $appFields
     * @param boolean $approvalRequired
     * @param MagicObject $approvalEntity
     * @return string
     */
    public function createGuiList($entityMain, $listFields, $filterFields, $approvalRequired, $entityApproval)
    {
        $entityName = $entityMain->getentityName();
        $primaryKey = $entityMain->getPrimaryKey();
        $objectName = lcfirst($entityName);
        $dom = new DOMDocument();
        $filterSection = $dom->createElement('div');
        $filterSection->setAttribute('class', 'filter-section');
        
        $filterSection->appendChild($dom->createTextNode("\n\t"));      
        $filterSection->appendChild($this->createFilterForm($dom, $listFields, $filterFields));
        $filterSection->appendChild($dom->createTextNode("\n"));
        
        $dataSection = $dom->createElement('div');
        $dataSection->setAttribute('class', 'data-section');
        
        $dataSection->appendChild($dom->createTextNode("\n\t".self::PHP_OPEN_TAG)); 
        
        $dataSection->appendChild($dom->createTextNode($this->beforeListScript($dom, $entityMain, $listFields, $filterFields, $objectName))); 
        
        $dataSection->appendChild($dom->createTextNode("\n\tif(\$pageData->getTotalResult() > 0)")); 
        
        
        
        $dataSection->appendChild($dom->createTextNode("\n\t".self::CURLY_BRACKET_OPEN)); 
        $dataSection->appendChild($dom->createTextNode("\n\t".self::PHP_CLOSE_TAG)); 
        
        $pagination1 = $dom->createTextNode($this->createPagination("pagination-top"));
        $pagination2 = $dom->createTextNode($this->createPagination("pagination-bottom"));
        
        $dataSection->appendChild($dom->createTextNode("\n")); 
        $dataSection->appendChild($pagination1);
        $dataSection->appendChild($dom->createTextNode("\n\t")); 
        
        $dataSection->appendChild($this->createDataList($dom, $listFields, $objectName, $primaryKey));
        
        $dataSection->appendChild($dom->createTextNode("\n")); 
        $dataSection->appendChild($pagination2);
        $dataSection->appendChild($dom->createTextNode("\n\t")); 
        $dataSection->appendChild($dom->createTextNode("\n\t".self::PHP_OPEN_TAG)); 
       
        

        $dataSection->appendChild($dom->createTextNode("\n")); 
        
        $dataSection->appendChild($dom->createTextNode($this->scriptWhenListNotFound())); 
        
        $dataSection->appendChild($dom->createTextNode("\n")); 


        $dom->appendChild($filterSection);
        $dom->appendChild($dataSection);
        $xml = $dom->saveXML();

        $htmlList = $this->xmlToHtml($xml);
        $htmlList = $this->fixTable($htmlList);
        $htmlList = $this->fixPhpCode($htmlList);
        $htmlList = trim($htmlList, self::NEW_LINE);
        
        $htmlList = $this->addTab($htmlList, 2);
        $htmlList = $this->addIndent($htmlList, 2);
        $htmlList = $this->addWrapper($htmlList, self::WRAPPER_LIST);

        
        $getData = array();
        $getData[] = $this->getIncludeHeader();
        $getData[] = $this->constructEntityLabel($entityName);

        $getData[] = self::PHP_CLOSE_TAG.self::NEW_LINE.$htmlList.self::NEW_LINE.self::PHP_OPEN_TAG;
        $getData[] = $this->getIncludeFooter();



        return 
        "\r\n{\r\n"
        .implode(self::NEW_LINE, $getData)
        .self::NEW_LINE
        .self::CURLY_BRACKET_CLOSE;
    }
    
    public function scriptWhenListNotFound()
    {
        $script = 
'}
else
{
?>
    <div class="alert alert-info"><?php echo $appLanguage->getMessageDataNotFound();?></div>
<?php
}
?>';
        $script = str_replace("\r\n", "\n", $script);
        $script = $this->addIndent($script, 1);
        $script = str_replace("\r\n", "\n", $script);
        
        return $script;
    }
    
    /**
     * Create pagination
     *
     * @param string $className
     * @return string
     */
    public function createPagination($className)
    {
        $script = 
'<div class="pagination '.$className.'">
    <div class="pagination-number">
    <?php
    foreach($rowData->getPagination() as $pg)
    {
        ?><span class="page-selector<?php echo $pg'."['selected'] ? ' page-selected':'';?>".'" data-page-number="<?php echo $pg'."['page'];?>".'"><a href="<?php echo PicoPagination::getPageUrl($pg'."['page']".');?>"><?php echo $pg'."['page']".';?></a></span><?php
    }
    ?>
    </div>
</div>';
        $script = str_replace("\r\n", "\n", $script);
        $script = $this->addIndent($script, 1);
        $script = str_replace("\r\n", "\n", $script);
        
        return $script;
        
    }
    
    /**
     * Create GUI LIST section 
     *
     * @param DOMDocument $dom
     * @param MagicObject $mainEntity
     * @param AppField[] $listFields
     * @param AppField[] $filterFields
     * @param string $objectName
     * @return string
     */
    public function beforeListScript($dom, $entityMain, $listFields, $filterFields, $objectName)
    {

        $arrFilter = array();
        $arrSort = array();
        foreach($filterFields as $field)
        {
            $arrFilter[] = '"'.PicoStringUtil::camelize($field->getFieldName()).'" => "'.PicoStringUtil::camelize($field->getFieldName()).'"';
        }
        foreach($listFields as $field)
        {
            $arrSort[] = '"'.PicoStringUtil::camelize($field->getFieldName()).'" => "'.PicoStringUtil::camelize($field->getFieldName()).'"';
        }
        $script = 
'
$specMap = array(
    '.implode(",\n\t", $arrFilter).'
);
$sortOrderMap = array(
    '.implode(",\n\t", $arrSort).'
);
            
$specification = PicoSpecification::fromUserInput($inputGet, $specMap);
$sortable = PicoSortable::fromUserInput($inputGet, $sortOrderMap);
$pageable = new PicoPageable(new PicoPage($inputGet->getPage(), $appConfig->getPageSize()), $sortable);
$dataLoader = new '.$entityMain->getEntityName().'(null, $database);
$pageData = $dataLoader->findAll($specification, $pagable, $sortable);
$resultSet = $pageData->getResult();
';

        $script = str_replace("\r\n", "\n", $script);
        $script = $this->addIndent($script, 1);
        $script = str_replace("\r\n", "\n", $script);
        
        return $script;
    }
    
    /**
     * Create list
     *
     * @param DOMDocument $dom
     * @param AppField[] $listFields
     * @param string $objectName
     * @return DOMElement
     */
    public function createDataList($dom, $listFields, $objectName, $primaryKey)
    {
        $form = $dom->createElement('form');
        $form->setAttribute('action', '');
        $form->setAttribute('method', 'post');
        $form->setAttribute('class', 'data-form');

        $div1 = $dom->createElement('div');
        $div1->setAttribute('class', 'data-wrapper');
        
        $table = $this->createDataTableList($dom, $listFields, $objectName, $primaryKey);
        
        $div1->appendChild($dom->createTextNode("\n\t\t\t")); 
        $div1->appendChild($table);
        $div1->appendChild($dom->createTextNode("\n\t\t")); 
        
        $form->appendChild($dom->createTextNode("\n\t\t")); 
        $form->appendChild($div1);
        $form->appendChild($dom->createTextNode("\n\t")); 
        
        
        
        return $form;
    }
    
    /**
     * Create table list
     *
     * @param DOMDocument $dom
     * @param AppField[] $listFields
     * @param string $primaryKey
     * @return DOMElement
     */
    public function createDataTableList($dom, $listFields, $objectName, $primaryKey)
    {
        $table = $dom->createElement('table');
        $table->setAttribute('class', 'table table-row');
        
        $thead = $this->createTableListHead($dom, $listFields, $objectName, $primaryKey);
        $tbody = $this->createTableListBody($dom, $listFields, $objectName, $primaryKey);
        
        $table->appendChild($dom->createTextNode("\n\t\t\t\t")); 
        $table->appendChild($thead);
        $table->appendChild($dom->createTextNode("\n\t\t\t")); 
        
        $table->appendChild($dom->createTextNode("\n\t\t\t\t")); 
        $table->appendChild($tbody);
        $table->appendChild($dom->createTextNode("\n\t\t\t")); 
        
        
        
        return $table;
    }
    
    /**
     * Create table list head
     *
     * @param DOMDocument $dom
     * @param AppField[] $listFields
     * @param string $objectName
     * @param string $primaryKey
     * @return DOMElement
     */
    public function createTableListHead($dom, $listFields, $objectName, $primaryKey)
    {
        
        $thead = $dom->createElement('thead');
        
        $trh = $dom->createElement('tr');

        // checkbox begin
        $td = $dom->createElement('td');
        $td->setAttribute('class', 'data-selector');
        $td->setAttribute('data-key', $primaryKey);
        $selector = '.checkbox-'.strtolower(str_replace('_', '-', $primaryKey));
        
        $chekcbox = $dom->createElement('input');
        $chekcbox->setAttribute('type', 'checkbox');
        $chekcbox->setAttribute('class', 'checkbox check-master');
        $chekcbox->setAttribute('data-selector', $selector);
        
        $td->appendChild($dom->createTextNode("\n\t\t\t\t\t\t\t")); 
        $td->appendChild($chekcbox);
        $td->appendChild($dom->createTextNode("\n\t\t\t\t\t\t")); 
        
        $trh->appendChild($dom->createTextNode("\n\t\t\t\t\t\t")); 
        $trh->appendChild($td);
        // checkbox end
        
        // edit begin
        $spanEdit = $dom->createElement('span');
        $spanEdit->setAttribute('class', 'fa fa-edit');
        $spanEdit->appendChild($dom->createTextNode(''));
        
        $td2 = $dom->createElement('td');
        
        $td2->appendChild($dom->createTextNode("\n\t\t\t\t\t\t\t")); 
        $td2->appendChild($spanEdit);
        $td2->appendChild($dom->createTextNode("\n\t\t\t\t\t\t")); 
        
        $trh->appendChild($dom->createTextNode("\n\t\t\t\t\t\t")); 
        $trh->appendChild($td2);
        // edit end
        
        // detail begin
        $spanDetail = $dom->createElement('span');
        $spanDetail->setAttribute('class', 'fa fa-folder');
        $spanDetail->appendChild($dom->createTextNode(''));
        
        $td2 = $dom->createElement('td');
        
        $td2->appendChild($dom->createTextNode("\n\t\t\t\t\t\t\t")); 
        $td2->appendChild($spanDetail);
        $td2->appendChild($dom->createTextNode("\n\t\t\t\t\t\t")); 
        
        $trh->appendChild($dom->createTextNode("\n\t\t\t\t\t\t")); 
        $trh->appendChild($td2);
        // detail end
        
        
        foreach($listFields as $field)
        {
            $td = $dom->createElement('td');
            $td->setAttribute('data-field', $field->getFieldName());
            $td->appendChild($dom->createTextNode(self::PHP_OPEN_TAG.self::ECHO.self::VAR."appEntityLabel".self::CALL_GET.PicoStringUtil::upperCamelize($field->getFieldName())."();".self::PHP_CLOSE_TAG)); 
            
            $trh->appendChild($dom->createTextNode("\n\t\t\t\t\t\t")); 
            $trh->appendChild($td);
        }
        
        $trh->appendChild($dom->createTextNode("\n\t\t\t\t\t")); 
        
        $thead->appendChild($dom->createTextNode("\n\t\t\t\t\t")); 
        $thead->appendChild($trh);
        $thead->appendChild($dom->createTextNode("\n\t\t\t\t")); 
        
        return $thead;
    }
    
    /**
     * Create table list body
     *
     * @param DOMDocument $dom
     * @param AppField[] $listFields
     * @param string $primaryKey
     * @return DOMElement
     */
    public function createTableListBody($dom, $listFields, $objectName, $primaryKey)
    {
        
        $tbody = $dom->createElement('tbody');
        
        $trh = $dom->createElement('tr');
        
        // checkbox begin
        $td = $dom->createElement('td');
        $td->setAttribute('class', 'data-selector');
        $td->setAttribute('data-key', $primaryKey);
        $upperPkName = PicoStringUtil::upperCamelize($primaryKey);
        $className = 'checkbox check-slave checkbox-'.strtolower(str_replace('_', '-', $primaryKey));
        
        $chekcbox = $dom->createElement('input');
        $chekcbox->setAttribute('type', 'checkbox');
        $chekcbox->setAttribute('class', $className);
        $chekcbox->setAttribute('name', 'checked_row_id[]');
        $chekcbox->setAttribute('value', self::PHP_OPEN_TAG.self::ECHO.self::VAR.$objectName.self::CALL_GET.$upperPkName."();".self::PHP_CLOSE_TAG);
        
        $td->appendChild($dom->createTextNode("\n\t\t\t\t\t\t\t")); 
        $td->appendChild($chekcbox);
        $td->appendChild($dom->createTextNode("\n\t\t\t\t\t\t")); 
        
        $trh->appendChild($dom->createTextNode("\n\t\t\t\t\t\t")); 
        $trh->appendChild($td);
        // checkbox end
        
        // edit begin
        $edit = $dom->createElement('a');
        $edit->setAttribute('class', 'edit-control');
        $href = '<?php echo $currentModule->getRedirectUrl(UserAction::UPDATE, '.$this->getStringOf($primaryKey).', $objectName->get'.$upperPkName.');?>';
        $edit->setAttribute('href', $href);
        $spanEdit = $dom->createElement('span');
        $spanEdit->setAttribute('class', 'fa fa-edit');
        $spanEdit->appendChild($dom->createTextNode(''));
        $edit->appendChild($spanEdit);
        
        $td2 = $dom->createElement('td');
        
        $td2->appendChild($dom->createTextNode("\n\t\t\t\t\t\t\t")); 
        $td2->appendChild($edit);
        $td2->appendChild($dom->createTextNode("\n\t\t\t\t\t\t")); 
        
        $trh->appendChild($dom->createTextNode("\n\t\t\t\t\t\t")); 
        $trh->appendChild($td2);
        // edit end
        
        // detail begin
        $detail = $dom->createElement('a');
        $detail->setAttribute('class', 'detail-control field-master');
        $href = '<?php echo $currentModule->getRedirectUrl(UserAction::DETAIL, '.$this->getStringOf($primaryKey).', $objectName->get'.$upperPkName.');?>';
        $detail->setAttribute('href', $href);
        $spanDetail = $dom->createElement('span');
        $spanDetail->setAttribute('class', 'fa fa-folder');
        $spanDetail->appendChild($dom->createTextNode(''));
        $detail->appendChild($spanDetail);
        
        $td2 = $dom->createElement('td');
        
        $td2->appendChild($dom->createTextNode("\n\t\t\t\t\t\t\t")); 
        $td2->appendChild($detail);
        $td2->appendChild($dom->createTextNode("\n\t\t\t\t\t\t")); 
        
        $trh->appendChild($dom->createTextNode("\n\t\t\t\t\t\t")); 
        $trh->appendChild($td2);
        // detail end
        
        foreach($listFields as $field)
        {
            $td = $dom->createElement('td');
            $td->setAttribute('data-field', $field->getFieldName());
            
            $value = $this->createDetailValue($dom, $objectName, $field);
            
            $td->appendChild($value); 
            
            $trh->appendChild($dom->createTextNode("\n\t\t\t\t\t\t")); 
            $trh->appendChild($td);
        }
        
        $trh->appendChild($dom->createTextNode("\n\t\t\t\t\t")); 
        
        $tbody->appendChild($dom->createTextNode("\n\t\t\t\t\t")); 
        $tbody->appendChild($dom->createTextNode(self::PHP_OPEN_TAG));
        $tbody->appendChild($dom->createTextNode("\n\t\t\t\t\t")); 
        $tbody->appendChild($dom->createTextNode("foreach(\$resultSet as \$dataIndex => \$album)")); 
        $tbody->appendChild($dom->createTextNode("\n\t\t\t\t\t".self::CURLY_BRACKET_OPEN)); 
        $tbody->appendChild($dom->createTextNode("\n\t\t\t\t\t")); 

        $tbody->appendChild($dom->createTextNode(self::PHP_CLOSE_TAG));  
        
        $tbody->appendChild($dom->createTextNode("\n\t\t\t\t\t")); 
        $tbody->appendChild($trh);
        $tbody->appendChild($dom->createTextNode("\n\t\t\t\t\t")); 
        
        $tbody->appendChild($dom->createTextNode(self::PHP_OPEN_TAG));
        $tbody->appendChild($dom->createTextNode("\n\t\t\t\t\t".self::CURLY_BRACKET_CLOSE)); 
        $tbody->appendChild($dom->createTextNode("\n\t\t\t\t\t")); 
        $tbody->appendChild($dom->createTextNode(self::PHP_CLOSE_TAG));  
        $tbody->appendChild($dom->createTextNode("\n\t\t\t\t")); 
        
        return $tbody;
    }

    /**
     * Create filter
     *
     * @param DOMDocument $dom
     * @param AppField[] $listFields
     * @param AppField[] $filterFields
     * @return DOMElement
     */
    public function createFilterForm($dom, $listFields, $filterFields)
    {
        $form = $dom->createElement('form');
        $form->setAttribute('action', '');
        $form->setAttribute('method', 'get');
        $form->setAttribute('class', 'filter-form');

        
        $form = $this->appendFilter($dom, $form, $filterFields);
        
        
        
        $submitWrapper = $dom->createElement('span');
        $submitWrapper->setAttribute('class', 'filter-group');

        $buttonSearch = $dom->createElement('input');
        $buttonSearch->setAttribute('type', 'submit');
        $buttonSearch->setAttribute('class', 'btn btn-success');
        $buttonSearch->setAttribute('value', self::PHP_OPEN_TAG.self::ECHO.self::VAR."appLanguage".self::CALL_GET."ButtonSearch();".self::PHP_CLOSE_TAG);
        $whiteSpace2 = $dom->createTextNode("\n\t\t\t");
        $submitWrapper->appendChild($whiteSpace2);
        $submitWrapper->appendChild($buttonSearch);
        $submitWrapper->appendChild($dom->createTextNode("\n\t\t"));
        
        $addWrapper = $dom->createElement('span');
        $addWrapper->setAttribute('class', 'filter-group');
        
        $buttonSearch = $dom->createElement('input');
        $buttonSearch->setAttribute('type', 'button');
        $buttonSearch->setAttribute('class', 'btn btn-primary');
        $buttonSearch->setAttribute('value', self::PHP_OPEN_TAG.self::ECHO.self::VAR."appLanguage".self::CALL_GET."ButtonAdd();".self::PHP_CLOSE_TAG);
        $buttonSearch->setAttribute('onlick', "window.location='".self::PHP_OPEN_TAG.self::ECHO.self::VAR."currentModule".self::CALL_GET."RedirectUrl(UserAction::CREATE);".self::PHP_CLOSE_TAG."'");
        $whiteSpace2 = $dom->createTextNode("\n\t\t\t");
        
        
        $addWrapper->appendChild($dom->createTextNode("\n\t\t\t"));
   
        $addWrapper->appendChild($buttonSearch);
        
        $whiteSpace4 = $dom->createTextNode("\n\t\t");
        
    
        $addWrapper->appendChild($whiteSpace4);

        
        $whiteSpace = $dom->createTextNode("\n\t\t");
        $form->appendChild($whiteSpace);
        
        $form->appendChild($submitWrapper);
        $form->appendChild($dom->createTextNode("\n\n\t\t"));

        $form->appendChild($addWrapper);

        $whiteSpace3 = $dom->createTextNode("\n\t");
        $form->appendChild($whiteSpace3);

        return $form;
    }
    
    /**
     * Append filter
     *
     * @param DOMDocument $dom
     * @param DOMElement $form
     * @param AppField[] $filterFields
     * @return DOMElement
     */
    public function appendFilter($dom, $form, $filterFields)
    {
        
        foreach($filterFields as $field)
        {
            $fieldName = $field->getFieldName();
            $labelStr = self::PHP_OPEN_TAG.self::ECHO.self::VAR.'appEntityLabel'.self::CALL_GET.PicoStringUtil::upperCamelize($fieldName)."();".self::PHP_CLOSE_TAG;
            $label = $dom->createTextNode($labelStr);
            
            $labelWrapper = $dom->createElement('span');
            $labelWrapper->setAttribute('class', 'filter-label');
            $labelWrapper->appendChild($label);

                
            if($field->getFilterElementType() == "text")
            {
                $form->appendChild($dom->createTextNode("\n\t\t"));
                
                $filterGroup = $dom->createElement('span');
                $filterGroup->setAttribute('class', 'filter-group');

                $input = $dom->createElement('input');
                $input->setAttribute('type', 'text');
                $input->setAttribute('name', $field->getFieldName());
                $input->setAttribute('class', 'form-control');
                
                $fieldName = PicoStringUtil::upperCamelize($field->getFieldName());

                
                $input->setAttribute('value', AppBuilderBase::PHP_OPEN_TAG.AppBuilderBase::ECHO.AppBuilderBase::VAR."inputGet".AppBuilderBase::CALL_GET.$fieldName."();".AppBuilderBase::PHP_CLOSE_TAG);
                $input->setAttribute('autocomplete', 'off');
                
                $filterGroup->appendChild($dom->createTextNode("\n\t\t\t"));
                
                $filterGroup->appendChild($labelWrapper);
                
                $filterGroup->appendChild($dom->createTextNode("\n\t\t\t"));
                
                $inputWrapper = $dom->createElement('span');
                $inputWrapper->setAttribute('class', 'filter-control');
                $inputWrapper->appendChild($dom->createTextNode("\n\t\t\t\t"));
                $inputWrapper->appendChild($input);
                $inputWrapper->appendChild($dom->createTextNode("\n\t\t\t"));
                
                $filterGroup->appendChild($inputWrapper);
                
                
                $filterGroup->appendChild($dom->createTextNode("\n\t\t"));
                
                $form->appendChild($filterGroup);
                
                $form->appendChild($dom->createTextNode("\n\t\t"));
                
                
            }
            else if($field->getFilterElementType() == "select")
            {
                $form->appendChild($dom->createTextNode("\n\t\t"));
                
                $filterGroup = $dom->createElement('span');
                $filterGroup->setAttribute('class', 'filter-group');

                $select = $dom->createElement('select');
                $select->setAttribute('name', $field->getFieldName());
                $select->setAttribute('class', 'form-control');
                
                $objectName = lcfirst($this->getObjectNameFromFieldName($field->getFieldName()));
                
                $inputGetName = PicoStringUtil::upperCamelize($field->getFieldName());

                $referenceFilter = $field->getReferenceFilter();
                
                $select = $this->appendOption($dom, $select, $objectName, $field, $referenceFilter, self::VAR."inputGet".self::CALL_GET.$inputGetName."()");

                $filterGroup->appendChild($dom->createTextNode("\n\t\t\t"));
                
                $filterGroup->appendChild($labelWrapper);
                
                $filterGroup->appendChild($dom->createTextNode("\n\t\t\t"));
                
                
                $inputWrapper = $dom->createElement('span');
                $inputWrapper->setAttribute('class', 'filter-control');
                $inputWrapper->appendChild($dom->createTextNode("\n\t\t\t\t\t"));
                $inputWrapper->appendChild($select);
                $inputWrapper->appendChild($dom->createTextNode("\n\t\t\t"));
                
                $filterGroup->appendChild($inputWrapper);
                                
                
                $filterGroup->appendChild($dom->createTextNode("\n\t\t"));
                

                
                $form->appendChild($filterGroup);
                
                $form->appendChild($dom->createTextNode("\n\t\t"));
            }
            
            
        }
        
        return $form;
    }
    
    public function getObjectNameFromFieldName($fieldName)
    {
        if(PicoStringUtil::endsWith($fieldName, "_id"))
        {
            return PicoStringUtil::camelize(substr($fieldName, 0, strlen($fieldName) - 3));
        }
        else
        {
            return PicoStringUtil::camelize($fieldName);
        }
    }

    /**
     * Create insert form table
     *
     * @param DOMDocument $dom
     * @param MagicObject $mainEntity
     * @param string $objectName
     * @param AppField[] $insertFields
     * @param string $primaryKeyName
     * @return DOMElement
     */
    private function createInsertFormTable($dom, $mainEntity, $objectName, $insertFields, $primaryKeyName)
    {
        $table = $this->createElementTableResponsive($dom);
        $tbody = $dom->createElement('tbody');
        foreach($insertFields as $field)
        {
            if($field->getIncludeInsert())
            {
                $tr = $this->createInsertRow($dom, $mainEntity, $objectName, $field, $primaryKeyName);
                $tbody->appendChild($tr);
            }
        }
        $table->appendChild($tbody);
        return $table;
    }
    
    /**
     * Create insert form table
     *
     * @param DOMDocument $dom
     * @param MagicObject $mainEntity
     * @param string $objectName
     * @param AppField[] $insertFields
     * @param string $primaryKeyName
     * @return DOMElement
     */
    private function createUpdateFormTable($dom, $mainEntity, $objectName, $insertFields, $primaryKeyName)
    {
        $table = $this->createElementTableResponsive($dom);
        $tbody = $dom->createElement('tbody');
        foreach($insertFields as $field)
        {
            if($field->getIncludeEdit())
            {
                $tr = $this->createUpdateRow($dom, $mainEntity, $objectName, $field, $primaryKeyName);
                $tbody->appendChild($tr);
            }
        }
        $table->appendChild($tbody);
        return $table;
    }
    
    /**
     * Create detail table
     *
     * @param DOMDocument $dom
     * @param MagicObject $mainEntity
     * @param string $objectName
     * @param AppField[] $insertFields
     * @param string $primaryKeyName
     * @return DOMElement
     */
    private function createDetailTable($dom, $mainEntity, $objectName, $insertFields, $primaryKeyName)
    {
        $table = $this->createElementTableResponsive($dom);
        $tbody = $dom->createElement('tbody');
        foreach($insertFields as $field)
        {
            if($field->getIncludeDetail())
            {
                $tr = $this->createDetailRow($dom, $mainEntity, $objectName, $field, $primaryKeyName);
                $tbody->appendChild($tr);
            }
        }
        $table->appendChild($tbody);
        return $table;
    }

    /**
     * Create detail compare table
     *
     * @param DOMDocument $dom
     * @param MagicObject $mainEntity
     * @param string $objectName
     * @param AppField[] $insertFields
     * @param string $primaryKeyName
     * @return DOMElement
     */
    private function createDetailTableCompare($dom, $mainEntity, $objectName, $insertFields, $primaryKeyName, $approvalEntity, $objectApprovalName)
    {
        $table = $this->createElementTableResponsive($dom);
        $tbody = $dom->createElement('tbody');
        foreach($insertFields as $field)
        {
            if($field->getIncludeDetail())
            {
                $tr = $this->createDetailCompareRow($dom, $mainEntity, $objectName, $field, $primaryKeyName, $approvalEntity, $objectApprovalName);
                $tbody->appendChild($tr);
            }
        }
        $table->appendChild($tbody);
        return $table;
    }

    /**
     * Create insert form table
     *
     * @param DOMDocument $dom
     * @param MagicObject $mainEntity
     * @param string $objectName
     * @param AppField $insertField
     * @param string $primaryKeyName
     * @return DOMElement
     */
    private function createInsertRow($dom, $mainEntity, $objectName, $field, $primaryKeyName)
    {
        $tr = $dom->createElement('tr');
        $td1 = $dom->createElement('td');
        $td2 = $dom->createElement('td');

        $upperFieldName = PicoStringUtil::upperCamelize($field->getFieldName());
        $caption = self::PHP_OPEN_TAG.self::ECHO.self::VAR."appEntityLabel".self::CALL_GET.$upperFieldName."();".self::PHP_CLOSE_TAG;
        $label = $dom->createTextNode($caption);

        $td1->appendChild($label);

        $input = $this->createInsertControl($dom, $mainEntity, $objectName, $field, $primaryKeyName, $field->getFieldName());
        if($input != null)
        {
            $td2->appendChild($input);
        }

        $tr->appendChild($td1);
        $tr->appendChild($td2);

        return $tr;
    }
    
    /**
     * Create insert form table
     *
     * @param DOMDocument $dom
     * @param MagicObject $mainEntity
     * @param string $objectName
     * @param AppField $insertField
     * @param string $primaryKeyName
     * @return DOMElement
     */
    private function createUpdateRow($dom, $mainEntity, $objectName, $field, $primaryKeyName)
    {
        $tr = $dom->createElement('tr');
        $td1 = $dom->createElement('td');
        $td2 = $dom->createElement('td');

        $upperFieldName = PicoStringUtil::upperCamelize($field->getFieldName());
        $caption = self::PHP_OPEN_TAG.self::ECHO.self::VAR."appEntityLabel".self::CALL_GET.$upperFieldName."();".self::PHP_CLOSE_TAG;
        $label = $dom->createTextNode($caption);

        $td1->appendChild($label);

        $input = $this->createUpdateControl($dom, $mainEntity, $objectName, $field, $primaryKeyName, $field->getFieldName());
        if($input != null)
        {
            $td2->appendChild($input);
        }

        $tr->appendChild($td1);
        $tr->appendChild($td2);

        return $tr;
    }
    
    /**
     * Create detail form table
     *
     * @param DOMDocument $dom
     * @param MagicObject $mainEntity
     * @param string $objectName
     * @param AppField $insertField
     * @param string $primaryKeyName
     * @return DOMElement
     */
    private function createDetailRow($dom, $mainEntity, $objectName, $field, $primaryKeyName)
    {
        $tr = $dom->createElement('tr');
        $td1 = $dom->createElement('td');
        $td2 = $dom->createElement('td');

        $upperFieldName = PicoStringUtil::upperCamelize($field->getFieldName());

        $caption = self::PHP_OPEN_TAG.self::ECHO.self::VAR."appEntityLabel".self::CALL_GET.$upperFieldName."();".self::PHP_CLOSE_TAG;
        $label = $dom->createTextNode($caption);


        $td1->appendChild($label);
        
        $value = $this->createDetailValue($dom, $objectName, $field);

        $td2->appendChild($value);
        

        $tr->appendChild($td1);
        $tr->appendChild($td2);

        return $tr;
    }
    
    /**
     * Create detail form table
     *
     * @param DOMDocument $dom
     * @param string $objectName
     * @param AppField $insertField
     * @return DOMElement
     */
    private function createDetailValue($dom, $objectName, $field)
    {
        $upperFieldName = PicoStringUtil::upperCamelize($field->getFieldName());
        
        $yes = self::VAR."appLanguage->getYes()";
        $no = self::VAR."appLanguage->getNo()";
        
        if($field->getElementType() == 'checkbox')
        {
            $val = "->option".$upperFieldName."(".$yes.", ".$no.");";
        }
        else
        {
            $val = "".self::CALL_GET.$upperFieldName."();";
        }
        
        return $dom->createTextNode(self::PHP_OPEN_TAG.self::ECHO.self::VAR.$objectName.$val.self::PHP_CLOSE_TAG);

    }

    /**
     * Create detail form table
     *
     * @param DOMDocument $dom
     * @param MagicObject $mainEntity
     * @param string $objectName
     * @param AppField $insertField
     * @param string $primaryKeyName
     * @return DOMElement
     */
    private function createDetailCompareRow($dom, $mainEntity, $objectName, $field, $primaryKeyName, $approvalEntity, $objectApprovalName)
    {
        $yes = self::VAR."appLanguage->getYes()";
        $no = self::VAR."appLanguage->getNo()";
        $tr = $dom->createElement('tr');
        $td1 = $dom->createElement('td');
        $td2 = $dom->createElement('td');
        $td3 = $dom->createElement('td');

        $upperFieldName = PicoStringUtil::upperCamelize($field->getFieldName());
        $caption = self::PHP_OPEN_TAG.self::ECHO.self::VAR."appEntityLabel".self::CALL_GET.$upperFieldName."();".self::PHP_CLOSE_TAG;
        $label = $dom->createTextNode($caption);
        
        if($field->getElementType() == 'checkbox')
        {
            $val = "->option".$upperFieldName."(".$yes.", ".$no.");";
        }
        else
        {
            $val = "".self::CALL_GET.$upperFieldName."();";
        }
        
        $value = $dom->createTextNode(self::PHP_OPEN_TAG.self::ECHO.self::VAR.$objectName.$val.self::PHP_CLOSE_TAG);
        $value2 = $dom->createTextNode(self::PHP_OPEN_TAG.self::ECHO.self::VAR.$objectApprovalName.$val.self::PHP_CLOSE_TAG);

        $td1->appendChild($label);
        
        $valueWrapper1 = $dom->createElement('span');
        $valueWrapper2 = $dom->createElement('span');
        
        $valueWrapper1->setAttribute('class', self::PHP_OPEN_TAG.self::ECHO."PicoTestUtil::classCompareData(".self::VAR.$objectName."->notEquals".$upperFieldName."(".self::VAR.$objectApprovalName.self::CALL_GET.$upperFieldName."()));".self::PHP_CLOSE_TAG);
        $valueWrapper2->setAttribute('class', self::PHP_OPEN_TAG.self::ECHO."PicoTestUtil::classCompareData(".self::VAR.$objectName."->notEquals".$upperFieldName."(".self::VAR.$objectApprovalName.self::CALL_GET.$upperFieldName."()));".self::PHP_CLOSE_TAG);

        $valueWrapper1->appendChild($value);
        $valueWrapper2->appendChild($value2);
        
        $td2->appendChild($valueWrapper1);
        $td3->appendChild($valueWrapper2);

        $tr->appendChild($td1);
        $tr->appendChild($td2);
        $tr->appendChild($td3);

        return $tr;
    }
    /**
     * Create insert form table
     *
     * @param DOMDocument $dom
     * @param MagicObject $mainEntity
     * @param string $objectName
     * @param AppField $insertField
     * @param string $primaryKeyName
     * @return DOMElement
     */
    private function createInsertControl($dom, $mainEntity, $objectName, $insertField, $primaryKeyName, $id = null)
    {
        $upperFieldName = PicoStringUtil::upperCamelize($insertField->getFieldName());
        $input = $dom->createElement('input');
        if($insertField->getElementType() == ElementType::TEXT)
        {
            $input = $dom->createElement('input');
            $input->setAttribute('autocomplete', 'off');
            $this->setInputTypeAttribute($input, $insertField->getDataType()); 
            $input->setAttribute('name', $insertField->getFieldName());

            $input = $this->addAttributeId($input, $id); 
            $input->setAttribute('autocomplete', 'off'); 
        }
        else if($insertField->getElementType() == ElementType::TEXTAREA)
        {
            $input = $dom->createElement('textarea');
            $classes = array();
            $classes[] = 'form-control';
            $input->setAttribute('class', implode(' ', $classes));
            $input->setAttribute('name', $insertField->getFieldName());

            $input = $this->addAttributeId($input, $id);  
            $value = $dom->createTextNode('');
            $input->appendChild($value);
            $input->setAttribute('spellcheck', 'false');
        }
        else if($insertField->getElementType() == ElementType::SELECT)
        {
            $input = $dom->createElement('select');
            $classes = array();
            $classes[] = 'form-control';
            $input->setAttribute('class', implode(' ', $classes));
            $input->setAttribute('name', $insertField->getFieldName());
            $input = $this->addAttributeId($input, $id);  
            $value = $dom->createElement('option');
            $caption = self::PHP_OPEN_TAG.self::ECHO.self::VAR."appLangauge->getSelectOne();".self::PHP_CLOSE_TAG;
            $textLabel = $dom->createTextNode($caption);
            $value->appendChild($textLabel);
            $value->setAttribute('value', '');
            $value->appendChild($textLabel);
            $input->appendChild($value);
            $referenceData = $insertField->getReferenceData();
            $input = $this->appendOption($dom, $input, $objectName, $insertField, $referenceData);
        }
        else if($insertField->getElementType() == ElementType::CHECKBOX)
        {
            $input = $dom->createElement('label');
            $inputStrl = $dom->createElement('input');
            $classes = array();
            $classes[] = 'form-check-input';
            $inputStrl->setAttribute('class', implode(' ', $classes));
            $inputStrl->setAttribute('type', 'checkbox');
            $inputStrl->setAttribute('name', $insertField->getFieldName());
            $inputStrl = $this->addAttributeId($inputStrl, $id);
            $inputStrl->setAttribute('value', '1');
            $input->appendChild($inputStrl);
            $caption = self::PHP_OPEN_TAG.self::ECHO.self::VAR."appEntityLabel".self::CALL_GET.$upperFieldName."();".self::PHP_CLOSE_TAG;
            $textLabel = $dom->createTextNode(' '.$caption);
            $input->appendChild($textLabel);
        }
        if($insertField->getRequired())
        {
            $input->setAttribute('required', 'required');
        }
        return $input;
    }
    
    /**
     * Create insert form table
     *
     * @param DOMDocument $dom
     * @param MagicObject $mainEntity
     * @param string $objectName
     * @param AppField $updateField
     * @param string $primaryKeyName
     * @return DOMElement
     */
    private function createUpdateControl($dom, $mainEntity, $objectName, $updateField, $primaryKeyName, $id = null)
    {
        $upperFieldName = PicoStringUtil::upperCamelize($updateField->getFieldName());
        $fieldName = $updateField->getFieldName();
        if($fieldName == $primaryKeyName)
        {
            $fieldName = "app_builder_new_pk_".$fieldName;
        }
        $input = $dom->createElement('input');
        if($updateField->getElementType() == ElementType::TEXT)
        {
            $input = $dom->createElement('input');
            $this->setInputTypeAttribute($input, $updateField->getDataType()); 
            $input->setAttribute('name', $fieldName);

            $input = $this->addAttributeId($input, $id);  
            
            $input->setAttribute('value', $this->createPhpOutputValue(self::VAR.$objectName.self::CALL_GET.$upperFieldName.'()'));
            $input->setAttribute('autocomplete', 'off');
        }
        else if($updateField->getElementType() == ElementType::TEXTAREA)
        {
            $input = $dom->createElement('textarea');
            $classes = array();
            $classes[] = 'form-control';
            $input->setAttribute('class', implode(' ', $classes));
            $input->setAttribute('name', $fieldName);

            $input = $this->addAttributeId($input, $id); 
            
            $value = $dom->createTextNode('');
            $input->appendChild($value);
            $value = $dom->createTextNode($this->createPhpOutputValue(self::VAR.$objectName.self::CALL_GET.$upperFieldName.'()'));
            $input->appendChild($value);
            $input->setAttribute('spellcheck', 'false');
        }
        else if($updateField->getElementType() == ElementType::SELECT)
        {
            $input = $dom->createElement('select');
            $classes = array();
            $classes[] = 'form-control';
            $input->setAttribute('class', implode(' ', $classes));
            $input->setAttribute('name', $fieldName);

            $input = $this->addAttributeId($input, $id);

            $value = $dom->createElement('option');
            $caption = self::PHP_OPEN_TAG.self::ECHO.self::VAR."appLangauge->getSelectOne();".self::PHP_CLOSE_TAG;
            $textLabel = $dom->createTextNode($caption);
            $value->appendChild($textLabel);
            $value->setAttribute('value', '');
            $value->appendChild($textLabel);
            $input->appendChild($value);
            $referenceData = $updateField->getReferenceData();
            $input = $this->appendOption($dom, $input, $objectName, $updateField, $referenceData, self::VAR.$objectName.self::CALL_GET.$upperFieldName.'()');
        }
        else if($updateField->getElementType() == ElementType::CHECKBOX)
        {
            $input = $dom->createElement('label');
            $inputStrl = $dom->createElement('input');
            $classes = array();
            $classes[] = 'form-check-input';
            $inputStrl->setAttribute('class', implode(' ', $classes));
            $inputStrl->setAttribute('type', 'checkbox');
            $inputStrl->setAttribute('name', $fieldName);

            $inputStrl = $this->addAttributeId($inputStrl, $id);

             
            $inputStrl->setAttribute('value', '1');
            $inputStrl->setAttribute("data-app-builder-encoded-script", base64_encode(self::PHP_OPEN_TAG.self::ECHO.self::VAR.$objectName.'->createChecked'.$upperFieldName.'();'.self::PHP_CLOSE_TAG));
            $input->appendChild($inputStrl);
            $caption = self::PHP_OPEN_TAG.self::ECHO.self::VAR."appEntityLabel".self::CALL_GET.$upperFieldName."();".self::PHP_CLOSE_TAG;
            $textLabel = $dom->createTextNode(' '.$caption);
            $input->appendChild($textLabel);

        }
        if($updateField->getRequired())
        {
            $input->setAttribute('required', 'required');
        }
        return $input;
    }

    /**
     * Add attribute id
     *
     * @param DOMElement $element
     * @param string $id
     * @return DOMElement
     */
    public function addAttributeId($element, $id)
    {
        if($id != null)
        {
            $element->setAttribute('id', $id);
        }  
        return $element;
    }
    
    private function appendOption($dom, $input, $objectName, $insertField, $referenceData, $selected = null)
    {
        if($referenceData != null)
        {        
            if($referenceData->getType() == 'map')
            {
                $map = $referenceData->getMap();
                $input = $this->appendOptionList($dom, $input, $map, $selected);
            }
            else if($referenceData->getType() == 'boolean')
            {
                $map = array(
                    (new MagicObject())->setValue('')->setLabel('<?php echo $appLanguage->getOptionLabelSelectOne();?>'),
                    (new MagicObject())->setValue('true')->setLabel('<?php echo $appLanguage->getOptionLabelYes();?>'),
                    (new MagicObject())->setValue('false')->setLabel('<?php echo $appLanguage->getOptionLabelNo();?>')
                );
                $input = $this->appendOptionList($dom, $input, $map, $selected);
            }
            else if($referenceData->getType() == 'entity')
            {      
                
                $entity = $referenceData->getEntity();
                $specification = $referenceData->getSpecification();
                $sortable = $referenceData->getSortable();
                $additionalOutput = $referenceData->getAdditionalOutput();
                
                if(isset($entity) && $entity->getEntityName() != null && $entity->getPrimaryKey() != null && $entity->getValue())
                {
                    $input = $this->appendOptionEntity($dom, $input, $entity, $specification, $sortable, $selected, $additionalOutput);
                }
            }
        }
        return $input;
    }
    /**
     * Create insert form table
     *
     * @param DOMDocument $dom
     * @param DOMElement $input
     * @param MagicObject $map
     * @param string $selected
     * @return DOMElement
     */
    private function appendOptionList($dom, $input, $map, $selected = null)
    {
        foreach($map as $opt)
        {
            $value = $opt->getValue();
            $caption = $opt->getLabel();
            $option = $dom->createElement('option');
            $option->setAttribute('value', $value);
            $textLabel = $dom->createTextNode($caption);
            $option->appendChild($textLabel);
            if($selected != null)
            {
                $input->setAttribute('data-app-builder-encoded-script', base64_encode('data-value="'.self::PHP_OPEN_TAG.self::ECHO.$selected.';'.self::PHP_CLOSE_TAG.'"'));
                $option->setAttribute("data-app-builder-encoded-script", base64_encode(self::PHP_OPEN_TAG.self::ECHO.'AttrUtil::selected('.$selected.', '."'".$value."'".');'.self::PHP_CLOSE_TAG));
            }
            else if($opt->isSelected())
            {
                $option->setAttribute('selected', 'selected');
            }
            $input->appendChild($dom->createTextNode("\n\t\t\t\t\t\t"));
            $input->appendChild($option);
        }
        $input->appendChild($dom->createTextNode("\n\t\t\t\t\t"));
        return $input;
    }
    
    /**
     * Append option from entiry
     *
     * @param DOMDocument $dom
     * @param DOMElement $input
     * @param MagicObject $entity
     * @param array $specification
     * @param array $sortable
     * @param string $selected
     * @param MagicObject $additionalOutput
     * @return DOMElement
     */
    private function appendOptionEntity($dom, $input, $entity, $specification, $sortable, $selected = null, $additionalOutput = null)
    {
        if($entity != null)
        {
            
            $paramAdditionalOutput = "";
            if($additionalOutput != null && !empty($additionalOutput))
            {
                
                $paramSelected = ($selected != null) ? ", $selected": ", null";
                $output = array();
                foreach($additionalOutput as $add)
                {
                    $output[] = $this->getStringOf(PicoStringUtil::camelize($add->getColumn()));
                }
                $paramAdditionalOutput = ', array('.implode(', ', $output).')';
                
            }
            else
            {
                
                $paramSelected = ($selected != null) ? ", $selected": "";
            }
            
            $specStr = $this->buildSpecification($specification);
            $sortStr = $this->buildSortable($sortable);

            $pk = $this->getStringOf(PicoStringUtil::camelize($entity->getPrimaryKey()));
            $val = $this->getStringOf(PicoStringUtil::camelize($entity->getValue()));
            
            $option = $dom->createTextNode(self::NEW_LINE_N.self::TAB3.self::TAB3
            .self::PHP_OPEN_TAG.self::ECHO.'FormBuilder::getInstance()'
            .'->showList(new '.$entity->getEntityName().'(null, '.self::VAR.$this->appConfig->getGlobalVariableDatabase().'), '
            .self::NEW_LINE_N.self::TAB3.self::TAB3
            .$specStr.', '.self::NEW_LINE_N.self::TAB3.self::TAB3
            .$sortStr.', '.self::NEW_LINE_N.self::TAB3.self::TAB3
            .$pk.', '.$val.$paramSelected.$paramAdditionalOutput.'); '.self::PHP_CLOSE_TAG.self::NEW_LINE_N.self::TAB3.self::TAB2);
            $input->appendChild($option);
        }
        return $input;
    }

    
    /**
     * Build specification
     *
     * @param array $specification
     * @return string
     */
    private function buildSpecification($specification)
    {
        $specs = array();
        $specs[] = 'PicoSpecification::getInstance()';
        if($specification != null)
        {
            foreach($specification as $spc)
            {
                if($spc->getColumn() != null && $spc->getValue() !== null)
                {
                    $upperField = PicoStringUtil::upperCamelize($spc->getColumn());
                    $value = $spc->getValue();
                    $value = $this->fixValue($value);
                    $specs[]  = self::NEW_LINE_N.self::TAB4.self::TAB3."->addAnd(PicoPredicate::getInstance()->set$upperField($value))";
                }
            }
        }
        return implode("", $specs);
    }
    
    /**
     * Build sortable
     *
     * @param array $sortable
     * @return string
     */
    private function buildSortable($sortable)
    {
        $specs = array();
        $specs[] = 'PicoSortable::getInstance()';
        if($sortable != null)
        {
            foreach($sortable as $srt)
            {
                if($srt->getSortBy() != null && $srt->getSortType() != null)
                {
                    $upperField = PicoStringUtil::upperCamelize($srt->getSortBy());
                    $type = $this->getSortType($srt->getSortType());
                    $specs[]  = self::NEW_LINE_N.self::TAB4.self::TAB3."->add(PicoSort::getInstance()->sortBy$upperField($type))";
                }
            }
        }
        return implode("", $specs);
    }
    
    /**
     * Get sort type
     *
     * @paramstring $sortType
     * @return string
     */
    public function getSortType($sortType)
    {
        if(stripos($sortType, 'PicoSort::') !== false)
        {
            return $sortType;
        }
        else
        {
            return '"'.$sortType.'"';
        }
    }

    /**
     * Fix value
     *
     * @param mixed $value
     * @return mixed
     */
    private function fixValue($value)
    {
        if(is_bool($value))
        {
            $value = ($value === true) ? 'true' : 'false';
        }
        else if(is_string($value))
        {
            $value = "'".$value."'";
        }
        return $value;
    }

    /**
     * Set input attribute
     *
     * @param DOMElement $input
     * @param string $dataType
     * @return DOMElement
     */
    private function setInputTypeAttribute($input, $dataType)
    {
        $classes = array();
        $classes[] = 'form-control';
        $input->setAttribute('class', implode(' ', $classes));
        if($dataType == 'int' || $dataType == 'integer')
        {
            $input->setAttribute('type', 'number');
        }
        else if($dataType == 'float' || $dataType == 'double')
        {
            $input->setAttribute('type', 'number');
            $input->setAttribute('step', 'any');
        }
        else
        {
            $input->setAttribute('type', $dataType);
        }
        return $input;
    }
    
    /**
     * Create button container table for create
     *
     * @param DOMDocument $dom
     * @return DOMElement
     */
    private function createButtonContainerTableCreate($dom, $name, $id, $userAction)
    {
        $table = $this->createElementTableResponsive($dom);
        
        $tbody = $dom->createElement('tbody');
        
        $tr2 = $dom->createElement('tr');
        $td1 = $dom->createElement('td');
        $td2 = $dom->createElement('td');
        
        $btn1 = $this->createSubmitButton($dom, $this->getTextOfLanguage('button_save'), $name, $id);
        $btn2 = $this->createCancelButton($dom, $this->getTextOfLanguage('button_cancel'), null, null, 'currentModule->getRedirectUrl()');
        
        $inputUserAction = $dom->createElement("input");
        $inputUserAction->setAttribute("type", "hidden");
        $inputUserAction->setAttribute("name", "user_action");
        $inputUserAction->setAttribute("value", '<?php echo '.$userAction.';?>');
        
        $td2->appendChild($dom->createTextNode("\n\t\t\t\t\t"));
        $td2->appendChild($btn1);
        $td2->appendChild($dom->createTextNode("\n\t\t\t\t\t"));
        $td2->appendChild($btn2);
        $td2->appendChild($dom->createTextNode("\n\t\t\t\t\t"));
        $td2->appendChild($inputUserAction);
        $td2->appendChild($dom->createTextNode("\n\t\t\t\t"));
              
        $tr2->appendChild($td1);
        $tr2->appendChild($td2);
        
        $tbody->appendChild($tr2);
        
        $table->appendChild($tbody);

        return $table;
    }

    /**
     * Create button container table for update
     *
     * @param DOMDocument $dom
     * @return DOMElement
     */
    private function createButtonContainerTableUpdate($dom, $name, $id, $userAction, $objectName, $primaryKeyName)
    {
        $upperPrimaryKeyName = PicoStringUtil::upperCamelize($primaryKeyName);
        $table = $this->createElementTableResponsive($dom);
        
        $tbody = $dom->createElement('tbody');
        
        $tr2 = $dom->createElement('tr');
        $td1 = $dom->createElement('td');
        $td2 = $dom->createElement('td');
        
        $btn1 = $this->createSubmitButton($dom, $this->getTextOfLanguage('button_save'), $name, $id);
        $btn2 = $this->createCancelButton($dom, $this->getTextOfLanguage('button_cancel'), null, null, 'currentModule->getRedirectUrl()');
        
        $inputUserAction = $dom->createElement("input");
        $inputUserAction->setAttribute("type", "hidden");
        $inputUserAction->setAttribute("name", "user_action");
        $inputUserAction->setAttribute("value", '<?php echo '.$userAction.';?>');
        
        $pkInput = $dom->createElement("input");
        $pkInput->setAttribute("type", "hidden");
        $pkInput->setAttribute("name", $primaryKeyName);
        $pkInput->setAttribute("value", '<?php echo $'.$objectName.'->get'.$upperPrimaryKeyName.'();?>');
        
        $td2->appendChild($dom->createTextNode("\n\t\t\t\t\t"));
        $td2->appendChild($btn1);
        $td2->appendChild($dom->createTextNode("\n\t\t\t\t\t"));
        $td2->appendChild($btn2);
        $td2->appendChild($dom->createTextNode("\n\t\t\t\t\t"));
        $td2->appendChild($inputUserAction);
        $td2->appendChild($dom->createTextNode("\n\t\t\t\t\t"));
        $td2->appendChild($pkInput);
        $td2->appendChild($dom->createTextNode("\n\t\t\t\t"));
              
        $tr2->appendChild($td1);
        $tr2->appendChild($td2);
        
        $tbody->appendChild($tr2);
        
        $table->appendChild($tbody);

        return $table;
    }
    
    /**
     * Create button container table
     *
     * @param DOMDocument $dom
     * @return DOMElement
     */
    private function createButtonContainerTableDetail($dom, $name, $id, $userAction, $objectName, $primaryKeyName)
    {
        $upperPrimaryKeyName = PicoStringUtil::upperCamelize($primaryKeyName);
        $table = $this->createElementTableResponsive($dom);
        
        $tbody = $dom->createElement('tbody');
        
        $tr2 = $dom->createElement('tr');
        $td1 = $dom->createElement('td');
        $td2 = $dom->createElement('td');
        
        $btn1 = $this->createCancelButton($dom, $this->getTextOfLanguage('button_update'), null, null, 'currentModule->getRedirectUrl(UserAction::UPDATE, Field::of()->'.$primaryKeyName.', $'.$objectName.'->get'.$upperPrimaryKeyName.'())');
        $btn2 = $this->createCancelButton($dom, $this->getTextOfLanguage('button_back_to_list'), null, null, 'currentModule->getRedirectUrl()');
        
        $td2->appendChild($dom->createTextNode("\n\t\t\t\t\t"));
        $td2->appendChild($btn1);
        $td2->appendChild($dom->createTextNode("\n\t\t\t\t\t"));
        $td2->appendChild($btn2);
        $td2->appendChild($dom->createTextNode("\n\t\t\t\t"));
              
        $tr2->appendChild($td1);
        $tr2->appendChild($td2);
        
        $tbody->appendChild($tr2);
        
        $table->appendChild($tbody);

        return $table;
    }
    
    /**
     * Create button container table for approval
     *
     * @param DOMDocument $dom
     * @return DOMElement
     */
    private function createButtonContainerTableApproval($dom, $name, $id, $userAction, $objectName, $primaryKeyName)
    {
        $upperPrimaryKeyName = PicoStringUtil::upperCamelize($primaryKeyName);
        $table = $this->createElementTableResponsive($dom);
        
        $tbody = $dom->createElement('tbody');
        
        $tr2 = $dom->createElement('tr');
        $td1 = $dom->createElement('td');
        $td2 = $dom->createElement('td');
        
        $btn1 = $this->createSubmitButton($dom, $this->getTextOfLanguage('button_approve'), 'action_approval', 'action_approval');
        $btn2 = $this->createSubmitButton($dom, $this->getTextOfLanguage('button_reject'), 'action_approval', 'action_approval');
        $btn3 = $this->createCancelButton($dom, $this->getTextOfLanguage('button_cancel'), null, null, 'currentModule->getRedirectUrl()');
        $btn4 = $this->createCancelButton($dom, $this->getTextOfLanguage('button_cancel'), null, null, 'currentModule->getRedirectUrl()');
        
        $inputUserActionApprove = $dom->createElement("input");
        $inputUserActionApprove->setAttribute("type", "hidden");
        $inputUserActionApprove->setAttribute("name", "user_action");
        $inputUserActionApprove->setAttribute("value", '<?php echo UserAction::APPROVE;?>');
        
        $inputUserActionReject = $dom->createElement("input");
        $inputUserActionReject->setAttribute("type", "hidden");
        $inputUserActionReject->setAttribute("name", "user_action");
        $inputUserActionReject->setAttribute("value", '<?php echo UserAction::REJECT;?>');
        
        $pkInputApprove = $dom->createElement("input");
        $pkInputApprove->setAttribute("type", "hidden");
        $pkInputApprove->setAttribute("name", $primaryKeyName);
        $pkInputApprove->setAttribute("value", '<?php echo $'.$objectName.'->get'.$upperPrimaryKeyName.'();?>');

        $pkInputReject = $dom->createElement("input");
        $pkInputReject->setAttribute("type", "hidden");
        $pkInputReject->setAttribute("name", $primaryKeyName);
        $pkInputReject->setAttribute("value", '<?php echo $'.$objectName.'->get'.$upperPrimaryKeyName.'();?>');

        
        $td2->appendChild($dom->createTextNode("\n\t\t\t\t\t<?php"));
        $td2->appendChild($dom->createTextNode("\n\t\t\t\t\t".'if($inputGet->getNextAction() == UserAction::APPROVE)'));
        $td2->appendChild($dom->createTextNode("\n\t\t\t\t\t{"));
        $td2->appendChild($dom->createTextNode("\n\t\t\t\t\t?>"));
        
        $td2->appendChild($dom->createTextNode("\n\t\t\t\t\t"));
        $td2->appendChild($btn1);
        $td2->appendChild($dom->createTextNode("\n\t\t\t\t\t"));
        $td2->appendChild($btn3);
        $td2->appendChild($dom->createTextNode("\n\t\t\t\t\t"));
        $td2->appendChild($inputUserActionApprove);
        $td2->appendChild($dom->createTextNode("\n\t\t\t\t\t"));
        $td2->appendChild($pkInputApprove);
        
        $td2->appendChild($dom->createTextNode("\n\t\t\t\t\t<?php"));
        $td2->appendChild($dom->createTextNode("\n\t\t\t\t\t}"));
        
        $td2->appendChild($dom->createTextNode("\n\t\t\t\t\t".'else'));
        $td2->appendChild($dom->createTextNode("\n\t\t\t\t\t{"));
        $td2->appendChild($dom->createTextNode("\n\t\t\t\t\t?>"));
        
        $td2->appendChild($dom->createTextNode("\n\t\t\t\t\t"));
        $td2->appendChild($btn2);
        $td2->appendChild($dom->createTextNode("\n\t\t\t\t\t"));
        $td2->appendChild($btn4);
        $td2->appendChild($dom->createTextNode("\n\t\t\t\t\t"));
        $td2->appendChild($inputUserActionReject);
        $td2->appendChild($dom->createTextNode("\n\t\t\t\t\t"));
        $td2->appendChild($pkInputReject);
        
        $td2->appendChild($dom->createTextNode("\n\t\t\t\t\t<?php"));
        $td2->appendChild($dom->createTextNode("\n\t\t\t\t\t}"));
        
        $td2->appendChild($dom->createTextNode("\n\t\t\t\t\t?>"));
        
        $td2->appendChild($dom->createTextNode("\n\t\t\t\t"));
              
        $tr2->appendChild($td1);
        $tr2->appendChild($td2);
        
        $tbody->appendChild($tr2);
        
        $table->appendChild($tbody);

        return $table;
    }

    /**
     * Convert XML to HTML
     *
     * @param string $xml
     * @return string
     */
    public function xmlToHtml($xml)
    {
        $start = stripos($xml, '<?xml');
        if($start !== false)
        {
            $end = stripos($xml, '?>', $start);
            if($end !== false)
            {
                $xml = substr($xml, $end+2);
            }
        }      
        do
        {
            $search = 'data-app-builder-encoded-script="';
            $startPos = strpos($xml, $search);
            if($startPos !== false)
            {
                $endPos = stripos($xml, '"', $startPos + strlen($search));
                $stringFound = substr($xml, $startPos, 1+$endPos-$startPos);
                $successor = $this->decodeString($stringFound);
                $xml = str_replace($stringFound, $successor, $xml);
            }
        }
        while($startPos !== false);
        return $xml;
    }
    
    /**
     * Decode string
     *
     * @param string $stringFound
     * @return string
     */
    public function decodeString($stringFound)
    {
        $search = 'data-app-builder-encoded-script="';
        if(PicoStringUtil::startsWith($stringFound, $search))
        {
            $code = substr($stringFound, strlen($search), strlen($stringFound) - strlen($search) - 1);     
        }
        else
        {
            $code = $stringFound;
        }
        return base64_decode($code);
    }
    
    /**
     * Create PHP output
     *
     * @param string $value
     * @return string
     */
    public function createPhpOutputValue($value)
    {
        return self::PHP_OPEN_TAG.self::ECHO.$value.';'.self::PHP_CLOSE_TAG;
    }
    
    /**
     * Get class function
     *
     * @param DOMElement $node
     * @return string[]
     */
    public function getClass($node)
    {
        $attr = $node->getAttribute('class');
        if($attr != null)
        {
            return explode(' ', $attr);
        }
        return array();
    }
    
    /**
     * Add class function
     *
     * @param DOMElement $node
     * @param string $class
     * @return DOMElement
     */
    public function addClass($node, $class)
    {
        $classes = $this->getClass($node);
        $classes[] = $class;
        $node->setAttribute('class', implode(' ', $classes));
        return $node;
    }
    
    /**
     * Remove class function
     *
     * @param DOMElement $node
     * @param string $class
     * @return DOMElement
     */
    public function removeClass($node, $class)
    {
        $classes = $this->getClass($node);
        $classesCopy = array();
        foreach($classes as $cls)
        {
            if($cls != $class)
            {
                $classesCopy[] = $cls;
            }
        }
        $node->setAttribute('class', implode(' ', $classesCopy));
        return $node;
    }
    
    /**
     * Create main entity function
     *
     * @param PicoDatabase $database
     * @param SecretObject $builderConfig
     * @param SecretObject $appConf
     * @param MagicObject $entityMain
     * @param EntityInfo $entityInfo
     * @return void
     */
    public function generateMainEntity($database, $builderConfig, $appConf, $entityMain, $entityInfo)
    {
        $entityName = $entityMain->getentityName();
        $tableName = $entityMain->getTableName();
        $baseDir = $appConf->getEntityBaseDirectory();
        $baseNamespace = $appConf->getEntityBaseNamespace();
        $generator = new AppEntityGenerator($database, $baseDir, $tableName, $baseNamespace, $entityName);
        $generator->generateCustomEntity($entityMain->getEntityName(), $entityMain->getTableName(), null, $this->getSucessorMainColumns());
    }
    
    /**
     * Create approval entity function
     *
     * @param PicoDatabase $database
     * @param SecretObject $builderConfig
     * @param SecretObject $appConf
     * @param MagicObject $entityMain
     * @param EntityInfo $entityInfo
     * @param MagicObject $entityApproval
     * @return void
     */
    public function generateApprovalEntity($database, $builderConfig, $appConf, $entityMain, $entityInfo, $entityApproval)
    {
        $entityName = $entityMain->getentityName();
        $tableName = $entityMain->getTableName();
        $baseDir = $appConf->getEntityBaseDirectory();
        $baseNamespace = $appConf->getEntityBaseNamespace();
        $generator = new AppEntityGenerator($database, $baseDir, $tableName, $baseNamespace, $entityName);

        $generator->generateCustomEntity($entityApproval->getEntityName(), $entityApproval->getTableName(), 
        $this->getPredecessorApprovalColumns($entityApproval), $this->getSucessorApprovalColumns(), true);
    }
    
    /**
     * Create trash entity function
     *
     * @param PicoDatabase $database
     * @param SecretObject $builderConfig
     * @param SecretObject $appConf
     * @param MagicObject $entityMain
     * @param EntityInfo $entityInfo
     * @param MagicObject $entityTrash
     * @return void
     */
    public function generateTrashEntity($database, $builderConfig, $appConf, $entityMain, $entityInfo, $entityTrash)
    {
        $entityName = $entityMain->getentityName();
        $tableName = $entityMain->getTableName();
        $baseDir = $appConf->getEntityBaseDirectory();
        $baseNamespace = $appConf->getEntityBaseNamespace();
        $generator = new AppEntityGenerator($database, $baseDir, $tableName, $baseNamespace, $entityName);
        $generator->generateCustomEntity($entityTrash->getEntityName(), $entityTrash->getTableName(),
        $this->getPredecessorTrashColumns($entityTrash), $this->getSucessorTrashColumns(), true);
    }

    /**
     * Get successor main columns
     *
     * @return array
     */
    public function getSucessorMainColumns()
    {
        $cols = array();
        
        if($this->appFeatures->isSortOrder())
        {
            $cols["sortOrder"]       = array('Type'=>'int(11)',     'Null'=>'YES', 'Key'=>'', 'Default'=>'NULL', 'Extra'=>''); //sort_order",
        }
        if($this->appFeatures->isActiavteDeactivate())
        {
            $cols["active"]          = array('Type'=>'tinyint(1)',  'Null'=>'YES', 'Key'=>'', 'Default'=>'0',    'Extra'=>''); //active",
        }
        
        if($this->appFeatures->isApprovalRequired())
        {
            $cols["adminAskEdit"]    = array('Type'=>'varchar(40)', 'Null'=>'YES', 'Key'=>'', 'Default'=>'NULL', 'Extra'=>''); //admin_ask_edit",
            $cols["ipAskEdit"]       = array('Type'=>'varchar(50)', 'Null'=>'YES', 'Key'=>'', 'Default'=>'NULL', 'Extra'=>''); //ip_ask_edit",
            $cols["timeAskEdit"]     = array('Type'=>'timestamp',   'Null'=>'YES', 'Key'=>'', 'Default'=>'NULL', 'Extra'=>''); //time_ask_edit",
            $cols["draft"]           = array('Type'=>'tinyint(1)',  'Null'=>'YES', 'Key'=>'', 'Default'=>'0',    'Extra'=>''); //draft",
            $cols["waitingFor"]      = array('Type'=>'int(4)',      'Null'=>'YES', 'Key'=>'', 'Default'=>'0',    'Extra'=>''); //waiting_for",
            $cols["approvalId"]      = array('Type'=>'varchar(40)', 'Null'=>'YES', 'Key'=>'', 'Default'=>'NULL', 'Extra'=>'');  //approval_id",
        }
        $result = array();
        foreach($cols as $key=>$value)
        {
            $value['Field'] = $this->entityInfo->get($key);
            $result[] = $value;
            
        }

        return $result;
    }
    
    /**
     * Get predecessor approval columns
     *
     * @param MagicObject $entityApproval
     * @return array
     */
    public function getPredecessorApprovalColumns($entityApproval)
    {
        return array(

            array('Field'=>$entityApproval->getPrimaryKey(), 'Type'=>'varchar(40)', 'Null'=>'YES', 'Key'=>'PRI', 'Default'=>'NULL', 'Extra'=>'')
        );
    }
    
    /**
     * Get successor approval columns
     *
     * @return array
     */
    public function getSucessorApprovalColumns()
    {
        $cols = array();
        
        if($this->appFeatures->isSortOrder())
        {
            $cols["sortOrder"]       = array('Type'=>'int(11)',     'Null'=>'YES', 'Key'=>'', 'Default'=>'NULL', 'Extra'=>''); //sort_order",
        }
        if($this->appFeatures->isActiavteDeactivate())
        {
            $cols["active"]          = array('Type'=>'tinyint(1)',  'Null'=>'YES', 'Key'=>'', 'Default'=>'0',    'Extra'=>''); //active",
        }
        if($this->appFeatures->isApprovalRequired())
        {
            $cols["adminAskEdit"]    = array('Type'=>'varchar(40)', 'Null'=>'YES', 'Key'=>'', 'Default'=>'NULL', 'Extra'=>''); //admin_ask_edit",
            $cols["ipAskEdit"]       = array('Type'=>'varchar(50)', 'Null'=>'YES', 'Key'=>'', 'Default'=>'NULL', 'Extra'=>''); //ip_ask_edit",
            $cols["timeAskEdit"]     = array('Type'=>'timestamp',   'Null'=>'YES', 'Key'=>'', 'Default'=>'NULL', 'Extra'=>''); //time_ask_edit",
            $cols["approvalStatus"]  = array('Type'=>'int(4)',      'Null'=>'YES', 'Key'=>'', 'Default'=>'0',    'Extra'=>''); //waiting_for",
        }
        $result = array();
        foreach($cols as $key=>$value)
        {
            $value['Field'] = $this->entityInfo->get($key);
            $result[] = $value;
        }
        return $result;
    }
    
    /**
     * Get predecessor trash columns
     *
     * @param MagicObject $entityTrash
     * @return array
     */
    public function getPredecessorTrashColumns($entityTrash)
    {
        return array(

            array('Field'=>$entityTrash->getPrimaryKey(), 'Type'=>'varchar(40)', 'Null'=>'YES', 'Key'=>'PRI', 'Default'=>'NULL', 'Extra'=>'')
        );
    }
    
    /**
     * Get successor approval columns
     *
     * @return array
     */
    public function getSucessorTrashColumns()
    {
        $cols = array();
        
        if($this->appFeatures->isSortOrder())
        {
            $cols["sortOrder"]       = array('Type'=>'int(11)',     'Null'=>'YES', 'Key'=>'', 'Default'=>'NULL', 'Extra'=>''); //sort_order",
        }
        if($this->appFeatures->isActiavteDeactivate())
        {
            $cols["active"]          = array('Type'=>'tinyint(1)',  'Null'=>'YES', 'Key'=>'', 'Default'=>'0',    'Extra'=>''); //active",
        }
        if($this->appFeatures->isApprovalRequired())
        {
            $cols["adminAskEdit"]    = array('Type'=>'varchar(40)', 'Null'=>'YES', 'Key'=>'', 'Default'=>'NULL', 'Extra'=>''); //admin_ask_edit",
            $cols["ipAskEdit"]       = array('Type'=>'varchar(50)', 'Null'=>'YES', 'Key'=>'', 'Default'=>'NULL', 'Extra'=>''); //ip_ask_edit",
            $cols["timeAskEdit"]     = array('Type'=>'timestamp',   'Null'=>'YES', 'Key'=>'', 'Default'=>'NULL', 'Extra'=>''); //time_ask_edit",
        }
        $result = array();
        foreach($cols as $key=>$value)
        {
            $value['Field'] = $this->entityInfo->get($key);
            $result[] = $value;
        }
        return $result;
    }

    /**
     * Get column info
     *
     * @return array
     */
    public function getColumnInfo()
    {
        return array(
            "adminCreate"  => array('Type'=>'varchar(40)', 'Null'=>'YES', 'Key'=>'', 'Default'=>'NULL', 'Extra'=>''), //admin_create",
            "adminEdit"    => array('Type'=>'varchar(40)', 'Null'=>'YES', 'Key'=>'', 'Default'=>'NULL', 'Extra'=>''), //admin_edit",
            "adminAskEdit" => array('Type'=>'varchar(40)', 'Null'=>'YES', 'Key'=>'', 'Default'=>'NULL', 'Extra'=>''), //admin_ask_edit",
            "ipCreate"     => array('Type'=>'varchar(50)', 'Null'=>'YES', 'Key'=>'', 'Default'=>'NULL', 'Extra'=>''), //ip_create",
            "ipEdit"       => array('Type'=>'varchar(50)', 'Null'=>'YES', 'Key'=>'', 'Default'=>'NULL', 'Extra'=>''), //ip_edit",
            "ipAskEdit"    => array('Type'=>'varchar(50)', 'Null'=>'YES', 'Key'=>'', 'Default'=>'NULL', 'Extra'=>''), //ip_ask_edit",
            "timeCreate"   => array('Type'=>'timestamp',   'Null'=>'YES', 'Key'=>'', 'Default'=>'NULL', 'Extra'=>''), //time_create",
            "timeEdit"     => array('Type'=>'timestamp',   'Null'=>'YES', 'Key'=>'', 'Default'=>'NULL', 'Extra'=>''), //time_edit",
            "timeAskEdit"  => array('Type'=>'timestamp',   'Null'=>'YES', 'Key'=>'', 'Default'=>'NULL', 'Extra'=>''), //time_ask_edit",
            "sortOrder"    => array('Type'=>'int(11)',     'Null'=>'YES', 'Key'=>'', 'Default'=>'NULL', 'Extra'=>''), //sort_order",
            "active"       => array('Type'=>'tinyint(1)',  'Null'=>'YES', 'Key'=>'', 'Default'=>'0',    'Extra'=>''), //active",
            "draft"        => array('Type'=>'tinyint(1)',  'Null'=>'YES', 'Key'=>'', 'Default'=>'0',    'Extra'=>''), //draft",
            "waitingFor"   => array('Type'=>'int(4)',      'Null'=>'YES', 'Key'=>'', 'Default'=>'0',    'Extra'=>''), //waiting_for",
            "approvalId"   => array('Type'=>'varchar(40)', 'Null'=>'YES', 'Key'=>'', 'Default'=>'NULL', 'Extra'=>'')  //approval_id",
        );
    }

    /**
     * Check if value is true
     *
     * @param mixed $value
     * @return boolean
     */
    public static function isTrue($value)
    {
        return $value == '1' || strtolower($value) == 'true' || $value === 1 || $value === true;
    }

    /**
     * Include app header
     *
     * @return string
     */
    public function getIncludeHeader()
    {
        return "require_once AppInclude::mainAppHeader(__DIR__, ".self::VAR.self::APP_CONFIG.");";
    }
    
    /**
     * Include app footer
     *
     * @return string
     */
    public function getIncludeFooter()
    {
        return "require_once AppInclude::mainAppFooter(__DIR__, ".self::VAR.self::APP_CONFIG.");";
    }
    
    /**
     * Construct entity label
     *
     * @param string $entityName
     * @return string
     */
    public function constructEntityLabel($entityName)
    {
        return self::VAR."appEntityLabel = new EntityLabel(new $entityName(), ".self::VAR.self::APP_CONFIG.");";
    }
    
    /**
     * Undocumented function
     *
     * @param string $html
     * @param integer $tab
     * @return string
     */
    public function addTab($html, $indent = 2)
    {
        $html = PicoStringUtil::windowsCariageReturn($html);
        $lines = explode(self::NEW_LINE, $html);
        foreach($lines as $index=>$line)
        {
            $line2 = ltrim($line, " ");
            $nspace = strlen($line) - strlen($line2);
            if($nspace % $indent == 0)
            {
                $ntab = (int) $nspace / $indent;
                $lines[$index] = $this->createTab($ntab).$line2;
            } 
        }
        return implode(self::NEW_LINE, $lines);
    }
    
    /**
     * Create tab
     *
     * @param integer $n
     * @return string
     */
    private function createTab($n)
    {
        $search = "";
        for($i = 0; $i<$n; $i++)
        {
            $search .= self::TAB1;
        }
        return $search;
    }

    /**
     * Undocumented function
     *
     * @param string $html
     * @param integer $tab
     * @return string
     */
    public function addIndent($html, $indent = 1)
    {
        $tab = "";
        for($i = 0; $i<$indent; $i++)
        {
            $tab .= self::TAB1;
        }
        $html = PicoStringUtil::windowsCariageReturn($html);
        $lines = explode(self::NEW_LINE, $html);
        foreach($lines as $index=>$line)
        {
            $lines[$index] = $tab.$line;
        }
        return implode(self::NEW_LINE, $lines);
    }

    /**
     * Add wrapper
     *
     * @param string $html
     * @param string $wrapper
     * @return string
     */
    public function addWrapper($html, $wrapper)
    {
        $html = rtrim($html, self::NEW_LINE).self::NEW_LINE;
        if($wrapper == self::WRAPPER_INSERT)
        {
            $html = 
            '<div class="page page-insert">'.self::NEW_LINE
            .self::TAB1.'<div class="row">'.self::NEW_LINE
            .$html
            .self::TAB1.'</div>'.self::NEW_LINE
            .'</div>'.self::NEW_LINE;
        }
        else if($wrapper == self::WRAPPER_UPDATE)
        {
            $html = 
            '<div class="page page-update">'.self::NEW_LINE
            .self::TAB1.'<div class="row">'.self::NEW_LINE
            .$html
            .self::TAB1.'</div>'.self::NEW_LINE
            .'</div>'.self::NEW_LINE;
        }
        else if($wrapper == self::WRAPPER_DETAIL)
        {
            $html = 
            '<div class="page page-detail">'.self::NEW_LINE
            .self::TAB1.'<div class="row">'.self::NEW_LINE
            .$html
            .self::TAB1.'</div>'.self::NEW_LINE
            .'</div>'.self::NEW_LINE;
        }
        else if($wrapper == self::WRAPPER_LIST)
        {
            $html = 
            '<div class="page page-list">'.self::NEW_LINE
            .self::TAB1.'<div class="row">'.self::NEW_LINE
            .$html
            .self::TAB1.'</div>'.self::NEW_LINE
            .'</div>'.self::NEW_LINE;
        }
        return rtrim($html, self::NEW_LINE);
    }

    /**
     * Get string of
     *
     * @param string $str
     * @return string
     */
    public function getStringOf($str)
    {
        if(strpos($str, " ") === false)
        {
            return 'Field::of()->'.$str;
        }
        else
        {
            return '"'.$str.'"';
        }
    }
    
}
