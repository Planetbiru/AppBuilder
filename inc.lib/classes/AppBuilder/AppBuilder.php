<?php

namespace AppBuilder;

use AppBuilder\Base\AppBuilderBase;
use MagicObject\MagicObject;
use MagicObject\Util\PicoStringUtil;

class AppBuilder extends AppBuilderBase
{
    /**
     * Create INSERT section without approval and trash
     *
     * @param AppField[] $appFields
     * @param MagicObject $mainEntity
     * @return string
     */
    public function createInsertSection($mainEntity, $appFields)
    {
        $entityName = $mainEntity->getEntityName();
        $objectName = lcfirst($entityName);
        $lines = array();
        
        $lines[] = "if(".parent::VAR."inputPost->getUserAction() == UserAction::CREATE)";
        $lines[] = parent::CURLY_BRACKET_OPEN;
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

        $lines[] = parent::TAB1.parent::VAR.$objectName.parent::CALL_SET.$upperAdminCreate."(".parent::VAR.$this->getCurrentAction()->getUserFunction().");";
        $lines[] = parent::TAB1.parent::VAR.$objectName.parent::CALL_SET.$upperTimeCreate."(".parent::VAR.$this->getCurrentAction()->getTimeFunction().");";
        $lines[] = parent::TAB1.parent::VAR.$objectName.parent::CALL_SET.$upperIpCreate."(".parent::VAR.$this->getCurrentAction()->getIpFunction().");";

        $upperAdminEdit = PicoStringUtil::upperCamelize($this->entityInfo->getAdminEdit());
        $upperTimeEdit = PicoStringUtil::upperCamelize($this->entityInfo->getTimeEdit());
        $upperIpEdit = PicoStringUtil::upperCamelize($this->entityInfo->getIpEdit());

        $lines[] = parent::TAB1.parent::VAR.$objectName.parent::CALL_SET.$upperAdminEdit."(".parent::VAR.$this->getCurrentAction()->getUserFunction().");";
        $lines[] = parent::TAB1.parent::VAR.$objectName.parent::CALL_SET.$upperTimeEdit."(".parent::VAR.$this->getCurrentAction()->getTimeFunction().");";
        $lines[] = parent::TAB1.parent::VAR.$objectName.parent::CALL_SET.$upperIpEdit."(".parent::VAR.$this->getCurrentAction()->getIpFunction().");";

        $lines[] = parent::TAB1.parent::VAR.$objectName.parent::CALL_INSERT_END;
        $lines[] = parent::CURLY_BRACKET_CLOSE;
        return implode(parent::NEW_LINE, $lines);
    }
    
    /**
     * Create UPDATE section without approval and trash
     *
     * @param AppField[] $appFields
     * @param MagicObject $mainEntity
     * @return string
     */
    public function createUpdateSection($mainEntity, $appFields)
    {
        $entityName = $mainEntity->getEntityName();
        $primaryKeyName = $mainEntity->getPrimaryKey();
        $upperPrimaryKeyName = PicoStringUtil::upperCamelize($primaryKeyName);
        $objectName = lcfirst($entityName);
        $updatePk = false;
        $lines = array();
        
        $lines[] = "if(".parent::VAR."inputPost->getUserAction() == UserAction::UPDATE)";
        $lines[] = parent::CURLY_BRACKET_OPEN;
        $lines[] = parent::TAB1.$this->createConstructor($objectName, $entityName);
        
        foreach($appFields as $field)
        {
            if($primaryKeyName == $field->getFieldName())
            {
                $updatePk = true;
            }
            $line = $this->createSetter($objectName, $field->getFieldName(), $field->getInputFilter());
            if($line != null)
            {
                $lines[] = $line;
            }
        }

        $upperAdminEdit = PicoStringUtil::upperCamelize($this->entityInfo->getAdminEdit());
        $upperTimeEdit = PicoStringUtil::upperCamelize($this->entityInfo->getTimeEdit());
        $upperIpEdit = PicoStringUtil::upperCamelize($this->entityInfo->getIpEdit());

        $lines[] = parent::TAB1.parent::VAR.$objectName.parent::CALL_SET.$upperAdminEdit."(".parent::VAR.$this->getCurrentAction()->getUserFunction().");";
        $lines[] = parent::TAB1.parent::VAR.$objectName.parent::CALL_SET.$upperTimeEdit."(".parent::VAR.$this->getCurrentAction()->getTimeFunction().");";
        $lines[] = parent::TAB1.parent::VAR.$objectName.parent::CALL_SET.$upperIpEdit."(".parent::VAR.$this->getCurrentAction()->getIpFunction().");";

        if(!$updatePk)
        {
            $line = $this->createSetter($objectName, $primaryKeyName, $this->getInputFilter($primaryKeyName));
            $lines[] = $line;
        }
        $lines[] = parent::TAB1.parent::VAR.$objectName.parent::CALL_UPDATE_END;
        
        if($updatePk)
        {
            $lines[] = ''; 
            $lines[] = parent::TAB1.'// update primary key value';      
            $lines[] = parent::TAB1.'$specification = PicoSpecification::getInstance()->addAnd(new PicoPredicate(Field::of()->'.PicoStringUtil::camelize($primaryKeyName).', $inputPost->get'.$upperPrimaryKeyName.'()));';
                
            $lines[] = parent::TAB1.$this->createConstructor($objectName, $entityName);
            $lines[] = parent::TAB1.parent::VAR.$objectName.'->where($specification)->set'.$upperPrimaryKeyName.'($inputPost->get'.PicoStringUtil::upperCamelize('app_builder_new_pk').$upperPrimaryKeyName.'())'.parent::CALL_UPDATE_END;
        }
        
        $lines[] = parent::CURLY_BRACKET_CLOSE;
        return implode(parent::NEW_LINE, $lines);
    }
    
    /**
     * Create UPDATE section without approval and trash
     *
     * @param MagicObject $mainEntity
     * @param mixed $withTrash
     * @param MagicObject $trashEntity
     * @return string
     */
    public function createDeleteSection($mainEntity, $withTrash = false, $trashEntity = null)
    {
        $entityName = $mainEntity->getEntityName();
        $pkName =  $mainEntity->getPrimaryKey();

        $objectName = lcfirst($entityName);
        
        $objectNameBk = $objectName;
        $lines = array();
        $upperPkName = PicoStringUtil::upperCamelize($pkName);
        
        $lines[] = "if(".parent::VAR."inputPost->getUserAction() == UserAction::DELETE)";
        $lines[] = parent::CURLY_BRACKET_OPEN;
        $lines[] = parent::TAB1."if(".parent::VAR."inputPost->countableCheckedRowId())";
        $lines[] = parent::TAB1.parent::CURLY_BRACKET_OPEN;
        $lines[] = parent::TAB1.parent::TAB1."foreach(".parent::VAR."inputPost->getCheckedRowId() as ".parent::VAR."rowId)";    
        $lines[] = parent::TAB1.parent::TAB1.parent::CURLY_BRACKET_OPEN;
            
        if($withTrash)
        {
            $entityTrashName = $trashEntity->getEntityName();
            $objectTrashName = lcfirst($entityTrashName);
            $lines[] = parent::TAB1.parent::TAB1.parent::TAB1.$this->createConstructor($objectNameBk, $entityName);
            $lines[] = parent::TAB1.parent::TAB1.parent::TAB1.parent::VAR.$objectNameBk."->findOneBy".$upperPkName."(".parent::VAR."rowId);";
            $lines[] = parent::TAB1.parent::TAB1.parent::TAB1."if(".parent::VAR.$objectNameBk."->hasValue".$upperPkName."())";
            $lines[] = parent::TAB1.parent::TAB1.parent::TAB1.parent::CURLY_BRACKET_OPEN;       
            $lines[] = parent::TAB1.parent::TAB1.parent::TAB1.parent::TAB1.$this->createConstructor($objectTrashName, $entityTrashName, $objectNameBk);
            $lines[] = parent::TAB1.parent::TAB1.parent::TAB1.parent::TAB1.parent::VAR.$objectTrashName.parent::CALL_INSERT_END;
            $lines[] = parent::TAB1.parent::TAB1.parent::TAB1.parent::TAB1.parent::VAR.$objectNameBk."->delete();";
            $lines[] = parent::TAB1.parent::TAB1.parent::TAB1.parent::CURLY_BRACKET_CLOSE;
        }
        else
        {
            $lines[] = parent::TAB1.parent::TAB1.parent::TAB1.$this->createConstructor($objectNameBk, $entityName);
            $lines[] = parent::TAB1.parent::TAB1.parent::TAB1.parent::VAR.$objectNameBk."->deleteOneBy".$upperPkName."(".parent::VAR."rowId);";
        }   
            
        $lines[] = parent::TAB1.parent::TAB1.parent::CURLY_BRACKET_CLOSE;
        $lines[] = parent::TAB1.parent::CURLY_BRACKET_CLOSE;
        $lines[] = parent::CURLY_BRACKET_CLOSE;
        
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
        $lines[] = "if(".parent::VAR."inputPost->getUserAction() == $userAction)";
        $lines[] = parent::CURLY_BRACKET_OPEN;
        $lines[] = parent::TAB1."if(".parent::VAR."inputPost->countableCheckedRowId())";
        $lines[] = parent::TAB1.parent::CURLY_BRACKET_OPEN;
        $lines[] = parent::TAB1.parent::TAB1."foreach(".parent::VAR."inputPost->getCheckedRowId() as ".parent::VAR."rowId)";    
        $lines[] = parent::TAB1.parent::TAB1.parent::CURLY_BRACKET_OPEN;
        $lines[] = parent::TAB1.parent::TAB1.parent::TAB1.$this->createConstructor($objectName, $entityName);
        $lines[] = parent::TAB1.parent::TAB1.parent::TAB1.parent::VAR.$objectName.parent::CALL_SET.$upperPkName."(".parent::VAR."rowId)->set".$upperActivationKey."($act)->update();";
        $lines[] = parent::TAB1.parent::TAB1.parent::CURLY_BRACKET_CLOSE;
        $lines[] = parent::TAB1.parent::CURLY_BRACKET_CLOSE;
        $lines[] = parent::CURLY_BRACKET_CLOSE;
        
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
        return $this->createActivationSectionBase($mainEntity, $activationKey, 'UserAction::ACTIVATE', true);
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
        return $this->createActivationSectionBase($mainEntity, $activationKey, 'UserAction::DEACTIVATE', false);
    }
}