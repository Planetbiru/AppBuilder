<?php

namespace AppBuilder;

use MagicObject\Generator\PicoColumnGenerator;
use MagicObject\Generator\PicoEntityGenerator;
use MagicObject\Util\PicoStringUtil;

class AppEntityGenerator extends PicoEntityGenerator
{
    
    /**
     * Generate custom entity
     *
     * @return string
     */
    public function generateCustomEntity($realEntityName = null, $realTableName = null, $predecessorField = null, $successorField = null)
    {
        $typeMap = $this->getTypeMap();
        if($realTableName != null)
        {
            $picoTableName = $realTableName;
        }
        else
        {
            $picoTableName = $this->tableName;
        }
        
        if($realEntityName != null)
        {
            $className = $realEntityName;
        }
        else if($this->entityName != null)
        {
            $className = $this->entityName;
        }
        else
        {
            $className = ucfirst(PicoStringUtil::camelize($picoTableName));
        }
        $fileName = $this->baseNamespace."/".$className;
        $path = $this->baseDir."/".$fileName.".php";
        $path = str_replace("\\", "/", $path);
        
        $dir = dirname($path);
        if(!file_exists($dir))
        {
            mkdir($dir, 0755, true);
        }

        $rows = PicoColumnGenerator::getColumnList($this->database, $picoTableName);
        if($predecessorField != null || $successorField != null)
        {
            $rows = $this->updateField($rows, $predecessorField, $successorField);
        }

        $attrs = array();
        if(is_array($rows))
        {
            foreach($rows as $row)
            {
                $columnName = $row['Field'];
                $columnType = $row['Type'];
                $columnKey = $row['Key'];
                $columnNull = $row['Null'];
                $columnDefault = $row['Default'];
                $columnExtra = $row['Extra'];

                $prop = $this->createProperty($typeMap, $columnName, $columnType, $columnKey, $columnNull, $columnDefault, $columnExtra);
                $attrs[] = $prop;
            }
        }
        
        $prettify = $this->prettify ? 'true' : 'false';

        $uses = array();
        $uses[] = "";

        $classStr = '<?php

namespace '.$this->baseNamespace.';

use MagicObject\MagicObject;'.implode("\r\n", $uses).'

/**
 * '.$className.' is entity of table '.$picoTableName.'. You can join this entity to other entity using annotation JoinColumn. 
 * Visit https://github.com/Planetbiru/MagicObject/blob/main/tutorial.md#entity
 * 
 * @Entity
 * @JSON(property-naming-strategy=SNAKE_CASE, prettify='.$prettify.')
 * @Table(name="'.$picoTableName.'")
 */
class '.$className.' extends MagicObject
{
'.implode("\r\n", $attrs).'
}';
        return file_put_contents($path, $classStr);
    }
    
    /**
     * Add predecessor and successor field and remove key and extra
     *
     * @param array $rows
     * @param array $predecessor
     * @param array $successor
     * @return array
     */
    private function updateField($rows, $predecessor = null, $successor = null)
    {
        $tmp = array();
        if($predecessor && is_array($predecessor))
        {
            foreach($predecessor as $row)
            {
                $tmp[] = $row;
            }
        }
        foreach($rows as $row)
        {
            $row['Key'] = "";
            $row['Extra'] = "";
            $tmp[] = $row;
        }
        if($successor && is_array($successor))
        {
            foreach($successor as $row)
            {
                $tmp[] = $row;
            }
        }
        return $tmp;
    }
}