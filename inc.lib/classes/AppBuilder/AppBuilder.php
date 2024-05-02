<?php

namespace AppBuilder;

use AppBuilder\Base\AppBuilderBase;
use AppBuilder\Base\AppBuilderInterface;
use MagicObject\MagicObject;
use MagicObject\Util\PicoStringUtil;

class AppBuilder extends AppBuilderBase
{
    /**
     * Create INSERT section without approval and trash
     *
     * @param AppField[] $appFields
     * @param string $entityName
     * @return string
     */
    public function createInsertSection($entityName, $appFields)
    {
        $objectName = lcfirst($entityName);
        $lines = array();
        
        $lines[] = "if(".parent::VAR."inputGet->getUserAction() == UserAction::INSERT)";
        $lines[] = "{";
        $lines[] = parent::TAB1.$this->createConstructor($objectName, $entityName);
        foreach($appFields as $field)
        {
            $line = $this->createSetter($objectName, $field->getFieldName(), $field->getInputFilter());
            if($line != null)
            {
                $lines[] = $line;
            }
        }

        $upperAdminCreate = PicoStringUtil::upperCamelize($this->entityInfo->getAdminCreate());
        $upperTimeCreate = PicoStringUtil::upperCamelize($this->entityInfo->getTimeCreate());
        $upperIpCreate = PicoStringUtil::upperCamelize($this->entityInfo->getIpCreate());

        $upperAdminEdit = PicoStringUtil::upperCamelize($this->entityInfo->getAdminEdit());
        $upperTimeEdit = PicoStringUtil::upperCamelize($this->entityInfo->getTimeEdit());
        $upperIpEdit = PicoStringUtil::upperCamelize($this->entityInfo->getIpEdit());

        $lines[] = parent::TAB1.parent::VAR.$objectName.parent::CALL_SET.$upperAdminCreate."(".parent::VAR.$this->getCurrentAction()->getUserFunction().");";
        $lines[] = parent::TAB1.parent::VAR.$objectName.parent::CALL_SET.$upperTimeCreate."(".parent::VAR.$this->getCurrentAction()->getTimeFunction().");";
        $lines[] = parent::TAB1.parent::VAR.$objectName.parent::CALL_SET.$upperIpCreate."(".parent::VAR.$this->getCurrentAction()->getIpFunction().");";

        $lines[] = parent::TAB1.parent::VAR.$objectName.parent::CALL_SET.$upperAdminEdit."(".parent::VAR.$this->getCurrentAction()->getUserFunction().");";
        $lines[] = parent::TAB1.parent::VAR.$objectName.parent::CALL_SET.$upperTimeEdit."(".parent::VAR.$this->getCurrentAction()->getTimeFunction().");";
        $lines[] = parent::TAB1.parent::VAR.$objectName.parent::CALL_SET.$upperIpEdit."(".parent::VAR.$this->getCurrentAction()->getIpFunction().");";

        $lines[] = parent::TAB1.parent::VAR.$objectName.parent::CALL_INSERT_END;
        $lines[] = "}";
        return implode(parent::NEW_LINE, $lines);
    }
    
    /**
     * Create UPDATE section without approval and trash
     *
     * @param AppField[] $appFields
     * @param string $entityName
     * @return string
     */
    public function createUpdateSection($entityName, $appFields)
    {
        $objectName = lcfirst($entityName);
        $lines = array();
        
        $lines[] = "if(".parent::VAR."inputGet->getUserAction() == UserAction::UPDATE)";
        $lines[] = "{";
        $lines[] = parent::TAB1.$this->createConstructor($objectName, $entityName);
        foreach($appFields as $field)
        {
            $line = $this->createSetter($objectName, $field->getFieldName(), $field->getInputFilter());
            if($line != null)
            {
                $lines[] = $line;
            }
        }
        $lines[] = parent::TAB1.parent::VAR.$objectName.parent::CALL_UPDATE_END;
        $lines[] = "}";
        return implode(parent::NEW_LINE, $lines);
    }
    
    /**
     * Create UPDATE section without approval and trash
     *
     * @param AppField[] $appFields
     * @param MagicObject $mainEntity
     * @param mixed $withTrash
     * @return string
     */
    public function createDeleteSection($mainEntity, $withTrash = false, $entityTrashName = null)
    {
        $entityName = $mainEntity->getEntityName();
        $pkName =  $mainEntity->getPrimaryKey();

        $objectName = lcfirst($entityName);
        $objectTrashName = lcfirst($entityTrashName);
        $objectNameBk = $objectName;
        $lines = array();
        $upperPkName = PicoStringUtil::upperCamelize($pkName);
        
        $lines[] = "if(".parent::VAR."inputGet->getUserAction() == UserAction::DELETE)";
        $lines[] = "{";
        $lines[] = parent::TAB1."if(".parent::VAR."inputPost->countableDeletionRowIds())";
        $lines[] = parent::TAB1."{";
        $lines[] = parent::TAB1.parent::TAB1."foreach(".parent::VAR."inputPost->getDeletionRowIds() as ".parent::VAR."rowId)";    
        $lines[] = parent::TAB1.parent::TAB1."{";
            
        if($withTrash)
        {

        $lines[] = parent::TAB1.parent::TAB1.parent::TAB1.$this->createConstructor($objectNameBk, $entityName);
        $lines[] = parent::TAB1.parent::TAB1.parent::TAB1.parent::VAR.$objectNameBk."->findOneBy".$upperPkName."(".parent::VAR."rowId);";
        $lines[] = parent::TAB1.parent::TAB1.parent::TAB1."if(".$objectNameBk."->hasValue".$upperPkName."())";
        $lines[] = parent::TAB1.parent::TAB1.parent::TAB1."{";       
        $lines[] = parent::TAB1.parent::TAB1.parent::TAB1.parent::TAB1.$this->createConstructor($objectTrashName, $entityTrashName, $objectNameBk);
        $lines[] = parent::TAB1.parent::TAB1.parent::TAB1.parent::TAB1.parent::VAR.$objectTrashName.parent::CALL_INSERT_END;
        $lines[] = parent::TAB1.parent::TAB1.parent::TAB1.parent::TAB1.parent::VAR.$objectNameBk."->delete();";
        $lines[] = parent::TAB1.parent::TAB1.parent::TAB1."}";

        }
        else
        {
            $lines[] = parent::TAB1.parent::TAB1.parent::TAB1.$this->createConstructor($objectNameBk, $entityName);
            $lines[] = parent::TAB1.parent::TAB1.parent::TAB1.parent::VAR.$objectNameBk."->deleteOneBy".$upperPkName."(".parent::VAR."rowId);";

        }    
            
            
        $lines[] = parent::TAB1.parent::TAB1."}";
        $lines[] = parent::TAB1."}";
        $lines[] = "}";
        
        return implode(parent::NEW_LINE, $lines);
    }
    
    /**
     * Create ACTIVATION section without approval and trash
     *
     * @param AppField[] $appFields
     * @param MagicObject $mainEntity
     * @param string $activationKey
     * @param boolean $activationValue
     * @return string
     */
    public function createActivationSectionBase($mainEntity, $activationKey, $userAction, $activationValue)
    {
        $entityName = $mainEntity->getEntityName();
        $pkName =  $mainEntity->getPrimaryKey();

        $objectName = lcfirst($entityName);
        $lines = array();
        $upperPkName = PicoStringUtil::upperCamelize($pkName);
        $upperActivationKey = PicoStringUtil::upperCamelize($activationKey);
        $act = $activationValue?'true':'false';
        $lines[] = "if(".parent::VAR."inputGet->getUserAction() == $userAction)";
        $lines[] = "{";
        $lines[] = parent::TAB1."if(".parent::VAR."inputPost->countableAtivationRowIds())";
        $lines[] = parent::TAB1."{";
        $lines[] = parent::TAB1.parent::TAB1."foreach(".parent::VAR."inputPost->getAtivationRowIds() as ".parent::VAR."rowId)";    
        $lines[] = parent::TAB1.parent::TAB1."{";
        $lines[] = parent::TAB1.parent::TAB1.parent::TAB1.$this->createConstructor($objectName, $entityName);
        $lines[] = parent::TAB1.parent::TAB1.parent::TAB1.parent::VAR.$objectName.parent::CALL_SET.$upperPkName."(".parent::VAR."rowId)->set".$upperActivationKey."($act)->update();";
        $lines[] = parent::TAB1.parent::TAB1."}";
        $lines[] = parent::TAB1."}";
        $lines[] = "}";
        
        return implode(parent::NEW_LINE, $lines);
    }
    
    /**
     * Create ACTIVATION section without approval and trash
     *
     * @param AppField[] $appFields
     * @param MagicObject $mainEntity
     * @param string $activationKey
     * @param boolean $activationValue
     * @return string
     */
    public function createActivationSection($mainEntity, $activationKey)
    {
        return $this->createActivationSectionBase($mainEntity, $activationKey, 'UserAction::ACTIVATION', true);
    }
    
    /**
     * Create DEACTIVATION section without approval and trash
     *
     * @param AppField[] $appFields
     * @param MagicObject $mainEntity
     * @param string $activationKey
     * @param boolean $activationValue
     * @return string
     */
    public function createDeactivationSection($mainEntity, $activationKey)
    {
        return $this->createActivationSectionBase($mainEntity, $activationKey, 'UserAction::DEACTIVATION', false);
    }
}