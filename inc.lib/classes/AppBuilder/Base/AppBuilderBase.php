<?php

namespace AppBuilder\Base;

use AppBuilder\AppSecretObject;
use AppBuilder\EntityInfo;
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
            return self::TAB1 . self::VAR . $objectName . "->set" . $method . "(" . self::VAR . "inputPost->get" . $method . "(PicoRequestConstant::" . $fieldFilter . "));";
        } else {
            return self::TAB1 . self::VAR . $objectName . "->set('" . $fieldName . "', " . self::VAR . "inputPost->get('" . $fieldName . "', PicoRequestConstant::" . $fieldFilter . "));";
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
}
