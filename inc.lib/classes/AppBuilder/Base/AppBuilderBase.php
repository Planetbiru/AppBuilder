<?php

namespace AppBuilder\Base;

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

class AppBuilderBase
{
    const TAB1 = "\t";
    const TAB2 = "\t\t";
    const TAB3 = "\t\t\t";
    const TAB4 = "\t\t\t\t";
    const NEW_LINE = "\r\n";
    const NEW_LINE_R = "\n";
    const VAR = "$";
    const CALL_INSERT_END = "->insert();";
    const CALL_UPDATE_END = "->update();";
    const CALL_DELETE_END = "->delete();";
    const CALL_SET = "->set";
    
    const PHP_OPEN_TAG = '<'.'?'.'php ';
    const PHP_CLOSE_TAG = ' ?'.'>';
    
    const STYLE_NATIVE = 'native';
    const STYLE_SETTER_GETTER = 'setter-getter';
    
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
     * Database
     *
     * @var PicoDatabase
     */
    protected $database;

    /**
     * AppBuilder config
     *
     * @var SecretObject
     */
    protected $appBuilderConfig;

    /**
     * Current action
     *
     * @var SecretObject
     */
    protected $currentAction;

    /**
     * Constructor
     *
     * @param PicoDatabase $database
     * @param SecretObject $appBuilderConfig
     * @param EntityInfo $entityInfo
     * @param EntityApvInfo $entityApvInfo
     */
    public function __construct($database, $appBuilderConfig, $entityInfo, $entityApvInfo)
    {
        $this->database = $database;
        $this->appBuilderConfig = $appBuilderConfig;
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
            $entityInfo->getWaitingFor()
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
        return self::VAR . $objectName . " = new $entityName($dataToLoad, " . self::VAR . $this->entityInfo->getDatabase() . ");";
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
            return self::TAB1 . self::VAR . $objectName . "->set" . $method . "(" . self::VAR . "inputPost->get" . $method . "(PicoFilterConstant::" . $fieldFilter . "));";
        } else {
            return self::TAB1 . self::VAR . $objectName . "->set('" . $fieldName . "', " . self::VAR . "inputPost->get('" . $fieldName . "', PicoFilterConstant::" . $fieldFilter . "));";
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
     * @return DOMElement
     */
    private function createElementForm($dom)
    {
        $form = $dom->createElement('form');
        $form->setAttribute('name', 'insertform');
        $form->setAttribute('id', 'insertform');
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
            $input->setAttribute('onclick', "window.location='".self::PHP_OPEN_TAG." echo ".self::VAR.$onclickUrlVariable.";".self::PHP_CLOSE_TAG."';");
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
            return self::PHP_OPEN_TAG.' echo '.self::VAR."currentLanguage->get$param"."(); ".self::PHP_CLOSE_TAG;
        }
        else
        {
            return self::PHP_OPEN_TAG.' echo '.self::VAR."currentLanguage->get('".$id."'); ".self::PHP_CLOSE_TAG;
        }
    }
    
    /**
     * Create GUI INSERT section without approval
     *
     * @param AppField[] $appFields
     * @param string $entityName
     * @param string $pkeyName
     * @param string $entityApprovalName
     * @return string
     */
    public function createGuiInsert($entityName, $insertFields, $pkName, $entityApprovalName)
    {
        $objectName = lcfirst($entityName);
        $dom = new DOMDocument();
        
        $form = $this->createElementForm($dom);
        
        $table1 = $this->createInsertFormTable($dom, $entityName, $objectName, $insertFields, $pkName);


        $table2 = $this->createButtonContainerTable($dom);

        $form->appendChild($table1);
        $form->appendChild($table2);
        
        $dom->appendChild($form);
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;

        $xml = $dom->saveXML();

        $html = $this->xmlToHtml($xml);
        $html = str_replace('<td/>', '<td></td>', $html);
        $html = str_replace(array('&lt;?php', '?&gt;', '-&gt;'), array('<'.'?'.'php', '?'.'>', '->'), $html);

        return "if(".self::VAR."inputGet->getUserAction() == UserAction::INSERT)\r\n"
        ."{\r\n"
        .'?'.">\r\n".$html.'<'.'?'."php\r\n"
        ."}\r\n";
    }

    /**
     * Create insert form table
     *
     * @param DOMDocument $dom
     * @param string $entityName
     * @param string $objectName
     * @param AppField[] $insertFields
     * @param string $pkName
     * @return DOMElement
     */
    private function createInsertFormTable($dom, $entityName, $objectName, $insertFields, $pkName)
    {
        $table = $this->createElementTableResponsive($dom);

        $tbody = $dom->createElement('tbody');

        foreach($insertFields as $field)
        {
            if($field->getIncludeInsert())
            {
                $tr = $this->createInsertRow($dom, $entityName, $objectName, $field, $pkName);
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
     * @param string $entityName
     * @param string $objectName
     * @param AppField $insertField
     * @param string $pkName
     * @return DOMElement
     */
    private function createInsertRow($dom, $entityName, $objectName, $field, $pkName)
    {
        $tr = $dom->createElement('tr');
        $td1 = $dom->createElement('td');
        $td2 = $dom->createElement('td');

        $label = $dom->createTextNode($field->getFieldLabel());

        $td1->appendChild($label);

        $input = $this->createInsertControl($dom, $entityName, $objectName, $field, $pkName, $field->getFieldName());
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
     * @param string $entityName
     * @param string $objectName
     * @param AppField $insertField
     * @param string $pkName
     * @return DOMElement
     */
    private function createInsertControl($dom, $entityName, $objectName, $insertField, $pkName, $id = null)
    {
        $input = $dom->createElement('input');
        if($insertField->getElementType() == ElementType::TEXT)
        {
            $input = $dom->createElement('input');
            $this->setInputTypeAttribute($input, $insertField->getDataType()); 
            $input->setAttribute('name', $insertField->getFieldName());

            if($id != null)
            {
                $input->setAttribute('id', $id);
            }   
        }
        else if($insertField->getElementType() == ElementType::TEXTAREA)
        {
            $input = $dom->createElement('textarea');
            $classes = array();
            $classes[] = 'form-control';
            $input->setAttribute('class', implode(' ', $classes));
            $input->setAttribute('name', $insertField->getFieldName());

            if($id != null)
            {
                $input->setAttribute('id', $id);
            }   
            $value = $dom->createTextNode('');
            $input->appendChild($value);
        }
        else if($insertField->getElementType() == ElementType::SELECT)
        {
            $input = $dom->createElement('select');
            $classes = array();
            $classes[] = 'form-control';
            $input->setAttribute('class', implode(' ', $classes));
            $input->setAttribute('name', $insertField->getFieldName());

            if($id != null)
            {
                $input->setAttribute('id', $id);
            }   
            $value = $dom->createElement('option');
            $textLabel = $dom->createTextNode('- Select One -');
            $value->appendChild($textLabel);
            $value->setAttribute('value', '');
            $value->appendChild($textLabel);
            $input->appendChild($value);
            
            $upperPkName = PicoStringUtil::upperCamelize($insertField->getFieldName());
            
            /*
            $input = $this->appendOptionList($dom, $input, $objectName, 
            array('SEN'=>'Senin','SEL'=>'Selasa','RAB'=>'Rabu','KAM'=>'Kamis','JUM'=>'Jumat','SAB'=>'Sabtu','MIN'=>'Minggu'),
            self::VAR.$objectName.'->get'.$upperPkName.'()'
            );
            */
            
            $input = $this->appendOptionEntity($dom, $input, $objectName, 
            array('name'=>'Producer','value'=>'producerId','text'=>'name'),
            self::VAR.$objectName.'->get'.$upperPkName.'()'
            );
            
            
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

            if($id != null)
            {
                $inputStrl->setAttribute('id', $id);
            }   
            $inputStrl->setAttribute('value', '1');
            $input->appendChild($inputStrl);
            $textLabel = $dom->createTextNode(' '.$insertField->getFieldLabel());
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
     * @param DOMElement $input
     * @param string $objectName
     * @param AppField $insertField
     * @param string $pkName
     * @return DOMElement
     */
    private function appendOptionList($dom, $input, $objectName, $values, $selected = null)
    {
        foreach($values as $key=>$value)
        {       
            $option = $dom->createElement('option');
            $option->setAttribute('value', $key);
            $textLabel = $dom->createTextNode($value);
            $option->appendChild($textLabel);
            if($selected != null)
            {
                $option->setAttribute("data-app-builder-encoded-script", base64_encode(self::PHP_OPEN_TAG.'echo AttrUtil::selected('.$selected.', '."'".$key."'".');'.self::PHP_CLOSE_TAG));
            }
            $input->appendChild($option);
            $input->setAttribute('data-app-builder-encoded-script', base64_encode('data-value="'.self::PHP_OPEN_TAG.'echo '.$selected.';'.self::PHP_CLOSE_TAG.'"'));
        }
        return $input;
    }
    
    /**
     * Append option from entiry
     *
     * @param DOMDocument $dom
     * @param DOMElement $input
     * @param string $objectName
     * @param MagicObject $entity
     * @param string $selected
     * @return DOMElement
     */
    private function appendOptionEntity($dom, $input, $objectName, $entity, $selected = null)
    {
        if($entity != null)
        {
            $paramSelected = ($selected != null) ? ", $selected": "";
            $option = $dom->createTextNode(self::NEW_LINE_R.self::TAB3
            .'<'.'?'.'php echo '.self::VAR.'selecOptionReference'
            .'->showList(new '.$entity['name'].'(null, '.self::VAR.'database), '.self::NEW_LINE_R.self::TAB3
            .'(new PicoSpecification())->addAnd(new PicoPredicate("'.$this->entityInfo->getActive().'", true))'
            .'->addAnd(new PicoPredicate("'.$this->entityInfo->getDraft().'", false)), '.self::NEW_LINE_R.self::TAB3
            .'(new PicoSortable())->add(new PicoSort("'.$entity['value'].'", PicoSort::ORDER_TYPE_ASC)), '.self::NEW_LINE_R.self::TAB3
            .'"'.$entity['value'].'", "'.$entity['text'].'"'.$paramSelected.'); '.'?'.'>');
            $input->appendChild($option);
        }
        return $input;
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
        /*
        <select class="form-control input-field-data-type" name="data_type_title" id="data_type_title">
        <option value="text" title="<input type=&quot;text&quot;>" selected="selected">text</option>
        <option value="email" title="<input type=&quot;email&quot;>">email</option>
        <option value="tel" title="<input type=&quot;tel&quot;>">tel</option>
        <option value="password" title="<input type=&quot;password&quot;>">password</option>
        <option value="int" title="<input type=&quot;number&quot;>">int</option>
        <option value="float" title="<input type=&quot;number&quot; step=&quot;any&quot;>">float</option>
        <option value="date" title="<input type=&quot;text&quot;>">date</option>
        <option value="time" title="<input type=&quot;text&quot;>">time</option>
        <option value="datetime" title="<input type=&quot;text&quot;>">datetime</option>
        <option value="color" title="<input type=&quot;text&quot;>">color</option>
        </select>
        */
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
    private function createButtonContainerTable($dom)
    {
        $table = $this->createElementTableResponsive($dom);
        
        
        $tbody2 = $dom->createElement('tbody');
        
        $tr2 = $dom->createElement('tr');
        $td1 = $dom->createElement('td');
        $td2 = $dom->createElement('td');
        
        $btn1 = $this->createSubmitButton($dom, $this->getTextOfLanguage('button_save'), "save-button", "save-insert");
        $btn2 = $this->createCancelButton($dom, $this->getTextOfLanguage('button_cancel'), null, null, 'currentModule');
        
        $space = $dom->createTextNode(" ");
        
        $td2->appendChild($btn1);
        $td2->appendChild($space);
        $td2->appendChild($btn2);
        
        
        $tr2->appendChild($td1);
        $tr2->appendChild($td2);
        
        $tbody2->appendChild($tr2);
        
        $table->appendChild($tbody2);

        return $table;
    }

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
                $replaced = true;
            }
        }
        while($startPos !== false);
        
        
        return $xml;
    }
    
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
    
}
