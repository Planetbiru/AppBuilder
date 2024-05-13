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
     * Constructor
     *
     * @param SecretObject $appBuilderConfig
     * @param SecretObject $appConfig
     * @param AppFeatures $appFeatures
     * @param EntityInfo $entityInfo
     * @param EntityApvInfo $entityApvInfo
     */
    public function __construct($appBuilderConfig, $appConfig, $appFeatures, $entityInfo, $entityApvInfo)
    {
        $this->appBuilderConfig = $appBuilderConfig;
        $this->appConfig = $appConfig;
        $this->appFeatures = $appFeatures;
        
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
    protected function createSetter($objectName, $fieldName, $fieldFilter)
    {
        if (in_array($fieldName, $this->skipedAutoSetter)) {
            return null;
        }
        if ($this->style = self::STYLE_SETTER_GETTER) {
            $method = PicoStringUtil::upperCamelize($fieldName);
            return self::TAB1 . self::VAR . $objectName . self::CALL_SET . $method . "(" . self::VAR . "inputPost".self::CALL_GET.$method . "(PicoFilterConstant::" . $fieldFilter . "));";
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
        return str_replace(array('&lt;?php', '?&gt;', '-&gt;'), array('<'.'?'.'php', '?'.'>', '->'), $html);
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
        $pkName =  $mainEntity->getPrimaryKey();

        $objectName = lcfirst($entityName);
        $dom = new DOMDocument();
        
        $form = $this->createElementForm($dom, 'insertform');
        
        $table1 = $this->createInsertFormTable($dom, $mainEntity, $objectName, $insertFields, $pkName);


        $table2 = $this->createButtonContainerTable($dom, "save-insert", "save-insert");

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
        
        return "if(".self::VAR."inputGet->getUserAction() == UserAction::INSERT)\r\n"
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
        $pkName =  $mainEntity->getPrimaryKey();
        $upperPkName = PicoStringUtil::upperCamelize($pkName);

        $objectName = lcfirst($entityName);
        $dom = new DOMDocument();
        
        $form = $this->createElementForm($dom, 'updateform');
        
        $table1 = $this->createUpdateFormTable($dom, $mainEntity, $objectName, $insertFields, $pkName);

        

        $table2 = $this->createButtonContainerTable($dom, "save-update", "save-update");

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
        $getData[] = $this->getIncludeFooter();
        $getData[] = self::TAB1.self::TAB1.self::CURLY_BRACKET_CLOSE;
        $getData[] = self::TAB1.self::TAB1."else";
        $getData[] = self::TAB1.self::TAB1.self::CURLY_BRACKET_OPEN;
        $getData[] = self::TAB1.self::TAB1.self::TAB1."// Do somtething here when data is not found";
        $getData[] = self::TAB1.self::TAB1.self::CURLY_BRACKET_CLOSE;
        $getData[] = self::TAB1.self::CURLY_BRACKET_CLOSE;
        $getData[] = self::TAB1."catch(Exception ".self::VAR."e)";
        $getData[] = self::TAB1.self::CURLY_BRACKET_OPEN;
        $getData[] = self::TAB1.self::TAB1."// Do somtething here when exception";
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
        $entityName = $mainEntity->getEntityName();
        $entityApprovalName = $approvalEntity->getEntityName();
        $objectApprovalName = PicoStringUtil::camelize($entityApprovalName);
        $pkName =  $mainEntity->getPrimaryKey();
        $upperPkName = PicoStringUtil::upperCamelize($pkName);

        $objectName = lcfirst($entityName);
        
        $htmlDetail = $this->createTableDetail($mainEntity, $objectName, $appFields, $pkName);

        $htmlDetailCompare = $this->createTableDetailCompare($mainEntity, $objectName, $appFields, $pkName, $approvalEntity, $objectApprovalName);

        $getData = array();
        $getData[] = self::TAB1.$this->createConstructor($objectName, $entityName);
        $getData[] = self::TAB1."try{";
        $getData[] = self::TAB1.self::TAB1.self::VAR.$objectName."->findOneBy".$upperPkName."(".self::VAR."inputGet".self::CALL_GET.$upperPkName."());";
        $getData[] = self::TAB1.self::TAB1."if(".self::VAR.$objectName."->hasValue".$upperPkName."())";
        $getData[] = self::TAB1.self::TAB1.self::CURLY_BRACKET_OPEN;

        $getData[] = self::TAB1.self::TAB1.self::TAB1."if(".self::VAR.$objectName."->nonNullApprovalId())";
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
        $getData[] = self::TAB1.self::TAB1.self::TAB1."// Do somtething here when data is not found";
        $getData[] = self::TAB1.self::TAB1.self::CURLY_BRACKET_CLOSE;
        $getData[] = self::TAB1.self::CURLY_BRACKET_CLOSE;
        $getData[] = self::TAB1."catch(Exception ".self::VAR."e)";
        $getData[] = self::TAB1.self::CURLY_BRACKET_OPEN;
        $getData[] = self::TAB1.self::TAB1."// Do somtething here when exception";
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
     * @param string $pkName
     * @return string
     */
    public function createTableDetail($mainEntity, $objectName, $appFields, $pkName)
    {
        $dom = new DOMDocument();
        
        $formDetail = $this->createElementForm($dom, 'detailform');
        $tableDetail1 = $this->createDetailTable($dom, $mainEntity, $objectName, $appFields, $pkName);
        $tableDetail2 = $this->createButtonContainerTable($dom, "save-update", "save-update");

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
     * @param string $pkName
     * @return string
     */
    public function createTableDetailCompare($mainEntity, $objectName, $appFields, $pkName, $approvalEntity, $objectApprovalName)
    {
        $dom = new DOMDocument();
        
        $formDetail = $this->createElementForm($dom, 'detailform');
        
        $upperWaitingFor = PicoStringUtil::upperCamelize($this->entityInfo->getWaitingFor());
        
        $div = $dom->createElement('div');
        $div->setAttribute('class', 'alert alert-warning');
        
        $messagePhp = self::PHP_OPEN_TAG.self::ECHO.self::VAR."appLanguage->message(".self::VAR.$objectName.self::CALL_GET.$upperWaitingFor."());".self::PHP_CLOSE_TAG;
        
        $message = $dom->createTextNode($messagePhp);

        $div->appendChild($message);
        
        $formDetail->appendChild($div);
        
        $tableDetail1 = $this->createDetailTableCompare($dom, $mainEntity, $objectName, $appFields, $pkName, $approvalEntity, $objectApprovalName);
        $tableDetail2 = $this->createButtonContainerTable($dom, "save-update", "save-update");

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
        $dom = new DOMDocument();
        $filterSection = $dom->createElement('div');
        $filterSection->setAttribute('class', 'filter-section');
        
        $whiteSpace = $dom->createTextNode("\n\t");
        $filterSection->appendChild($whiteSpace);
        
        $filterSection->appendChild($this->createFilterForm($dom, $listFields, $filterFields));
        $dataSection = $dom->createElement('div');
        $dataSection->setAttribute('class', 'data-section');
        
        $cariageReturn = $dom->createTextNode("\n");
        $filterSection->appendChild($cariageReturn);

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
        "{\r\n"
        .implode(self::NEW_LINE, $getData)
        .self::NEW_LINE
        .self::CURLY_BRACKET_CLOSE;
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
        
        $whiteSpace4 = $dom->createTextNode("\n\t\t");
        $submitWrapper->appendChild($whiteSpace4);

        
        $whiteSpace = $dom->createTextNode("\n\t\t");
        $form->appendChild($whiteSpace);
        
        $form->appendChild($submitWrapper);

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
     * @param string $pkName
     * @return DOMElement
     */
    private function createInsertFormTable($dom, $mainEntity, $objectName, $insertFields, $pkName)
    {
        $table = $this->createElementTableResponsive($dom);
        $tbody = $dom->createElement('tbody');
        foreach($insertFields as $field)
        {
            if($field->getIncludeInsert())
            {
                $tr = $this->createInsertRow($dom, $mainEntity, $objectName, $field, $pkName);
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
     * @param string $pkName
     * @return DOMElement
     */
    private function createUpdateFormTable($dom, $mainEntity, $objectName, $insertFields, $pkName)
    {
        $table = $this->createElementTableResponsive($dom);
        $tbody = $dom->createElement('tbody');
        foreach($insertFields as $field)
        {
            if($field->getIncludeEdit())
            {
                $tr = $this->createUpdateRow($dom, $mainEntity, $objectName, $field, $pkName);
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
     * @param string $pkName
     * @return DOMElement
     */
    private function createDetailTable($dom, $mainEntity, $objectName, $insertFields, $pkName)
    {
        $table = $this->createElementTableResponsive($dom);
        $tbody = $dom->createElement('tbody');
        foreach($insertFields as $field)
        {
            if($field->getIncludeDetail())
            {
                $tr = $this->createDetailRow($dom, $mainEntity, $objectName, $field, $pkName);
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
     * @param string $pkName
     * @return DOMElement
     */
    private function createDetailTableCompare($dom, $mainEntity, $objectName, $insertFields, $pkName, $approvalEntity, $objectApprovalName)
    {
        $table = $this->createElementTableResponsive($dom);
        $tbody = $dom->createElement('tbody');
        foreach($insertFields as $field)
        {
            if($field->getIncludeDetail())
            {
                $tr = $this->createDetailCompareRow($dom, $mainEntity, $objectName, $field, $pkName, $approvalEntity, $objectApprovalName);
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
     * @param string $pkName
     * @return DOMElement
     */
    private function createInsertRow($dom, $mainEntity, $objectName, $field, $pkName)
    {
        $tr = $dom->createElement('tr');
        $td1 = $dom->createElement('td');
        $td2 = $dom->createElement('td');

        $upperFieldName = PicoStringUtil::upperCamelize($field->getFieldName());
        $caption = self::PHP_OPEN_TAG.self::ECHO.self::VAR."appEntityLabel".self::CALL_GET.$upperFieldName."();".self::PHP_CLOSE_TAG;
        $label = $dom->createTextNode($caption);

        $td1->appendChild($label);

        $input = $this->createInsertControl($dom, $mainEntity, $objectName, $field, $pkName, $field->getFieldName());
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
     * @param string $pkName
     * @return DOMElement
     */
    private function createUpdateRow($dom, $mainEntity, $objectName, $field, $pkName)
    {
        $tr = $dom->createElement('tr');
        $td1 = $dom->createElement('td');
        $td2 = $dom->createElement('td');

        $upperFieldName = PicoStringUtil::upperCamelize($field->getFieldName());
        $caption = self::PHP_OPEN_TAG.self::ECHO.self::VAR."appEntityLabel".self::CALL_GET.$upperFieldName."();".self::PHP_CLOSE_TAG;
        $label = $dom->createTextNode($caption);

        $td1->appendChild($label);

        $input = $this->createUpdateControl($dom, $mainEntity, $objectName, $field, $pkName, $field->getFieldName());
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
     * @param string $pkName
     * @return DOMElement
     */
    private function createDetailRow($dom, $mainEntity, $objectName, $field, $pkName)
    {
        $yes = self::VAR."appLanguage->getYes()";
        $no = self::VAR."appLanguage->getNo()";
        $tr = $dom->createElement('tr');
        $td1 = $dom->createElement('td');
        $td2 = $dom->createElement('td');

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

        $td1->appendChild($label);

        $td2->appendChild($value);
        

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
     * @param string $pkName
     * @return DOMElement
     */
    private function createDetailCompareRow($dom, $mainEntity, $objectName, $field, $pkName, $approvalEntity, $objectApprovalName)
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
     * @param string $pkName
     * @return DOMElement
     */
    private function createInsertControl($dom, $mainEntity, $objectName, $insertField, $pkName, $id = null)
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
     * @param AppField $insertField
     * @param string $pkName
     * @return DOMElement
     */
    private function createUpdateControl($dom, $mainEntity, $objectName, $insertField, $pkName, $id = null)
    {
        $upperFieldName = PicoStringUtil::upperCamelize($insertField->getFieldName());
        $input = $dom->createElement('input');
        if($insertField->getElementType() == ElementType::TEXT)
        {
            $input = $dom->createElement('input');
            $this->setInputTypeAttribute($input, $insertField->getDataType()); 
            $input->setAttribute('name', $insertField->getFieldName());

            $input = $this->addAttributeId($input, $id);  
            
            $input->setAttribute('value', $this->createPhpOutputValue(self::VAR.$objectName.self::CALL_GET.$upperFieldName.'()'));
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
            $value = $dom->createTextNode($this->createPhpOutputValue(self::VAR.$objectName.self::CALL_GET.$upperFieldName.'()'));
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
            $input = $this->appendOption($dom, $input, $objectName, $insertField, $referenceData, self::VAR.$objectName.self::CALL_GET.$upperFieldName.'()');
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
            $inputStrl->setAttribute("data-app-builder-encoded-script", base64_encode(self::PHP_OPEN_TAG.self::ECHO.self::VAR.$objectName.'->createChecked'.$upperFieldName.'();'.self::PHP_CLOSE_TAG));
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
            $input->appendChild($option);
        }
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
                if($spc->getColumn() != null && $spc->getValue() != null)
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
     * Create button container table
     *
     * @param DOMDocument $dom
     * @return DOMElement
     */
    private function createButtonContainerTable($dom, $name, $id)
    {
        $table = $this->createElementTableResponsive($dom);
        
        $tbody = $dom->createElement('tbody');
        
        $tr2 = $dom->createElement('tr');
        $td1 = $dom->createElement('td');
        $td2 = $dom->createElement('td');
        
        $btn1 = $this->createSubmitButton($dom, $this->getTextOfLanguage('button_save'), $name, $id);
        $btn2 = $this->createCancelButton($dom, $this->getTextOfLanguage('button_cancel'), null, null, 'selfPath');
        
        $space = $dom->createTextNode(" ");
        
        $td2->appendChild($btn1);
        $td2->appendChild($space);
        $td2->appendChild($btn2);
              
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
