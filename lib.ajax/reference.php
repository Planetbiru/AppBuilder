<?php
use AppBuilder\AppSecretObject;
use MagicObject\Database\PicoDatabaseQueryBuilder;
use MagicObject\Database\PicoPredicate;
use MagicObject\Database\PicoSpecification;
use MagicObject\MagicObject;
use MagicObject\Request\InputGet;
use MagicObject\Util\ClassUtil\PicoObjectParser;
use MagicObject\Util\PicoStringUtil;

require_once dirname(__DIR__) . "/inc.app/app.php";
require_once dirname(__DIR__) . "/inc.app/database.php";
require_once dirname(__DIR__) . "/inc.app/sessions.php";

try
{
    $inputGet = new InputGet();
    $referenceConfig = new AppSecretObject();
    if($curApp != null && $curApp->getId() != null)
    {
        $referenceConfigPath = dirname(__DIR__) . "/inc.cfg/applications/".$curApp->getId()."/reference.yml";
        if(file_exists($referenceConfigPath))
        {
            $referenceConfig->loadYamlFile($referenceConfigPath, false, true, true);
        }
    }

    $reference = new AppSecretObject($referenceConfig->getReferenceData());
    error_log($reference);

    $fieldName = $inputGet->getFieldName();
    $camelFieldName = PicoStringUtil::camelize($fieldName);
    if(PicoStringUtil::endsWith($fieldName, "_id"))
    {
        $entityName = PicoStringUtil::upperCamelize(substr($fieldName, 0, strlen($fieldName)-3));
    }
    else
    {
        $entityName = PicoStringUtil::upperCamelize($fieldName);
    }
    $fieldReference = $reference->get($fieldName);
    error_log($fieldReference);
    if($fieldReference == null || $fieldReference->empty())
    {
        $fieldReference = new AppSecretObject($fieldName);
        $fieldReference->setType("entity");
        $entity = new AppSecretObject();
        $entity->setEntityName($entityName);
        $entity->setPrimaryKey($camelFieldName);
        $entity->setValue(PicoStringUtil::camelize('name'));
        $additionalOutput = array(
            PicoStringUtil::camelize('brith_day'),
            PicoStringUtil::camelize('phone_number')
        );
        $entity->setAdditionalOutput($additionalOutput);
        $specification = array(
            (new AppSecretObject())->setColumn("active")->setValue(true),
            (new AppSecretObject())->setColumn("draft")->setValue(false)
        );
        $sortable = array(
            (new AppSecretObject())->setOrderBy("sortOrder")->setOrderType('PicoSort:SORT_ASC'),
            (new AppSecretObject())->setOrderBy($camelFieldName)->setOrderType('PicoSort:SORT_ASC')
        );
        $entity->setSpecification($specification);
        $entity->setSortable($sortable);

        $fieldReference->setEntity($entity);    
        $reference->set($fieldName, $fieldReference);

        $referenceConfig->setReferenceData($reference);
        file_put_contents($referenceConfigPath, $referenceConfig->dumpYaml());
    }
    header("Content-type: application/json");
    echo $fieldReference;
}
catch(Exception $e)
{
    // do nothing
}