<?php

namespace AppBuilder\Base;

use AppBuilder\AppSecretObject;
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
    const VAR = "$";
    const CALL_INSERT_END = "->insert();";
    const CALL_UPDATE_END = "->update();";
    const CALL_DELETE_END = "->delete();";
    const CALL_SET = "->set";
    const STYLE_NATIVE = 'native';
    const STYLE_SETTER_GETTER = 'setter-getter';
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
    protected $entitiInfo;

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
     * @param EntityInfo $entitiInfo
     */
    public function __construct($database, $appBuilderConfig, $entitiInfo)
    {
        $this->database = $database;
        $this->appBuilderConfig = $appBuilderConfig;
        $this->currentAction = new AppSecretObject($appBuilderConfig->getCurrentAction());
        $this->configBaseDirectory = $appBuilderConfig->getConfigBaseDirectory();
        $this->entitiInfo = $entitiInfo;
        $this->skipedAutoSetter = array(
            $entitiInfo->draft,
            $entitiInfo->adminCreate,
            $entitiInfo->adminEdit,
            $entitiInfo->adminAskEdit,
            $entitiInfo->ipCreate,
            $entitiInfo->ipEdit,
            $entitiInfo->ipAskEdit,
            $entitiInfo->timeCreate,
            $entitiInfo->timeEdit,
            $entitiInfo->timeAskEdit,
            $entitiInfo->waitingFor
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
        return self::VAR . $objectName . " = new $entityName($dataToLoad, " . self::VAR . $this->entitiInfo->getDatabase() . ");";
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
    public function getEntitiInfo()
    {
        return $this->entitiInfo;
    }

    /**
     * Set entity info
     *
     * @param  EntityInfo  $entitiInfo  Entity info
     *
     * @return  self
     */
    public function setEntitiInfo($entitiInfo)
    {
        $this->entitiInfo = $entitiInfo;

        return $this;
    }

    public function createDefiner()
    {
        return '<tr>
        <td class="field-name">album_id<input type="hidden" name="field" value="album_id"></td>
        <td><input type="text" name="caption_album_id" value="Album" autocomplete="off" spellcheck="false"></td>
        <td align="center"><input type="checkbox" class="include_insert" name="include_insert_album_id" value="1" checked="checked"></td>
        <td align="center"><input type="checkbox" class="include_edit" name="include_edit_album_id" value="1" checked="checked"></td>
        <td align="center"><input type="checkbox" class="include_detail" name="include_detail_album_id" value="1" checked="checked"></td>
        <td align="center"><input type="checkbox" class="include_list" name="include_list_album_id" value="1" checked="checked"></td>
        <td align="center"><input type="checkbox" class="include_key" name="include_key_album_id" value="1"></td>
        <td align="center"><input type="checkbox" class="include_required" name="include_required_album_id" value="1"></td>
        <td align="center"><input type="radio" name="element_type_album_id" value="text" checked="checked"></td>
        <td align="center"><input type="radio" name="element_type_album_id" value="textarea"></td>
        <td align="center"><input type="radio" name="element_type_album_id" value="select"></td>
        <td align="center"><input type="radio" name="element_type_album_id" value="checkbox"></td>
        <td align="center"><input type="checkbox" name="list_filter_album_id" value="text" class="list_filter"></td>
        <td align="center"><input type="checkbox" name="list_filter_album_id" value="select" class="list_filter"></td>
        <td>
            <select name="data_type_album_id" id="data_type_album_id">
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
        </td>
        <td>
      <select name="filter_type_album_id" id="filter_type_album_id">
      <option value="FILTER_SANITIZE_NUMBER_INT">NUMBER_INT</option>
      <option value="FILTER_SANITIZE_NUMBER_UINT">NUMBER_UINT</option>
      <option value="FILTER_SANITIZE_NUMBER_OCTAL">NUMBER_OCTAL</option>
      <option value="FILTER_SANITIZE_NUMBER_HEXADECIMAL">NUMBER_HEXADECIMAL</option>
      <option value="FILTER_SANITIZE_NUMBER_FLOAT">NUMBER_FLOAT</option>
      <option value="FILTER_SANITIZE_STRING">STRING</option>
      <option value="FILTER_SANITIZE_STRING_INLINE">STRING_INLINE</option>
      <option value="FILTER_SANITIZE_NO_DOUBLE_SPACE">NO_DOUBLE_SPACE</option>
      <option value="FILTER_SANITIZE_STRIPPED">STRIPPED</option>
      <option value="FILTER_SANITIZE_SPECIAL_CHARS" selected="selected">SPECIAL_CHARS</option>
      <option value="FILTER_SANITIZE_ALPHA">ALPHA</option>
      <option value="FILTER_SANITIZE_ALPHANUMERIC">ALPHANUMERIC</option>
      <option value="FILTER_SANITIZE_ALPHANUMERICPUNC">ALPHANUMERICPUNC</option>
      <option value="FILTER_SANITIZE_STRING_BASE64">STRING_BASE64</option>
      <option value="FILTER_SANITIZE_EMAIL">EMAIL</option>
      <option value="FILTER_SANITIZE_URL">URL</option>
      <option value="FILTER_SANITIZE_IP">IP</option>
      <option value="FILTER_SANITIZE_ENCODED">ENCODED</option>
      <option value="FILTER_SANITIZE_COLOR">COLOR</option>
      <option value="FILTER_SANITIZE_MAGIC_QUOTES">MAGIC_QUOTES</option>
      <option value="FILTER_SANITIZE_PASSWORD">PASSWORD</option>
      </select>  </td>
      </tr>';
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
        error_log($value);
        $input->setAttribute('value', $value);
        if($onclickUrlVariable != null)
        {
            $input->setAttribute('onclick', "window.location='<"."?"."php echo ".self::VAR.$onclickUrlVariable.";?".">'");
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
        return '<'.'?'.'php echo '.self::VAR."currentLanguage->get('".$id."'); ?".'>';
    }
    
    /**
     * Create GUI INSERT section without approval
     *
     * @param AppField[] $appFields
     * @param string $entityName
     * @param string $pkeyName
     * @param string $entityApprovalName
     * @return DOMDocument
     */
    public function createGuiInsert($entityName, $insertFields, $pkName, $entityApprovalName)
    {
        $dom = new DOMDocument();
        
        $form = $this->createElementForm($dom);
        
        $table1 = $this->createElementTableResponsive($dom);
        $table2 = $this->createElementTableResponsive($dom);
        
        
        $tbody2 = $dom->createElement('tbody');
        
        $tr2 = $dom->createElement('tr');
        $td1 = $dom->createElement('td');
        $td2 = $dom->createElement('td');
        
        $btn1 = $this->createSubmitButton($dom, $this->getTextOfLanguage('button.save'), "save-button", "save-insert");
        $btn2 = $this->createCancelButton($dom, $this->getTextOfLanguage('button.cancel'), null, null, 'currentModule');
        


        $space = $dom->createTextNode("\r\n");
        
        $td2->appendChild($btn1);
        $td2->appendChild($space);
        $td2->appendChild($btn2);
        
        
        $tr2->appendChild($td1);
        $tr2->appendChild($td2);
        
        $tbody2->appendChild($tr2);
        
        $table2->appendChild($tbody2);

        $form->appendChild($table1);
        $form->appendChild($table2);
        
        
        /*
        <form name="editform" id="editform" action="" method="post">
            <table class="responsive responsive-two-cols" border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td>Kode</td>
                    <td><input type="text" class="form-control input-text input-text-plain" required="required" name="pendidikan_id" id="pendidikan_id" value="<?php echo $d_pendidikan_id; ?>"></td>
                </tr>
                <tr>
                    <td>Nama</td>
                    <td><input type="text" class="form-control input-text input-text-plain" required="required" name="nama" id="nama" value="<?php echo $d_nama; ?>"></td>
                </tr>
                <tr>
                    <td>Order</td>
                    <td><input type="number" class="form-control input-text input-text-plain" name="sort_order" id="sort_order" value="<?php echo $d_sort_order; ?>"></td>
                </tr>
                <tr>
                    <td>Default</td>
                    <td><label><input type="checkbox" name="default_data" id="default_data" value="1"<?php echo ($d_default_data == "1")?" checked=\"checked\"":""; ?>> Default</label></td>
                </tr>
                <tr>
                    <td>Aktif</td>
                    <td><label><input type="checkbox" name="aktif" id="aktif" value="1"<?php echo $cms->formChecked($d_aktif); ?>> Aktif</label></td>
                </tr>
                <tr>
                    <td>Catatan</td>
                    <td><textarea id="apv_note" name="apv_note" class="form-control"></textarea></td>
                </tr>
            </table>
            <table class="responsive responsive-two-cols responsive-button-area" border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td>&nbsp;</td>
                    <td>
                        <input type="submit" class="btn btn-success" id="save" name="button_save" value="Simpan">
                        <input type="button" class="btn btn-primary" id="showall" value="Tampilkan Semua" onclick="window.location='<?php echo $picoSelfName; ?>'">
                    </td>
                </tr>
            </table>
            </form>
            */
        $dom->appendChild($form);
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        return $dom;
    }
}
