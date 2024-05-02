<?php

namespace AppBuilder;

use AppBuilder\Base\AppBuilderBase;
use MagicObject\MagicObject;
use MagicObject\Util\PicoStringUtil;

class AppBuilderApproval extends AppBuilderBase
{
    /**
     * Create INSERT section without approval and trash
     *
     * @param MagicObject $mainEntity
     * @param AppField[] $appFields
     * @param boolean $approvalRequired
     * @param MagicObject $approvalEntity
     * @return string
     */
    public function createInsertApprovalSection($mainEntity, $appFields, $approvalRequired, $approvalEntity)
    {
        $entityName = $mainEntity->getEntityName();
        $objectName = lcfirst($entityName);
        
        $entityApprovalName = $approvalEntity->getEntityName();
        
        $objectApprovalName = lcfirst($entityApprovalName);
        $upperWaitingFor = PicoStringUtil::upperCamelize($this->entityInfo->getWaitingFor());
        $upperDraft = PicoStringUtil::upperCamelize($this->entityInfo->getDraft());

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
        
        // set draft
        $lines[] = parent::TAB1.parent::VAR.$objectName."->set".$upperDraft."(true);";
        $lines[] = parent::TAB1.parent::VAR.$objectName."->set".$upperWaitingFor."(".WaitingFor::CREATE.");";

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

        $lines[] = "";

        $lines[] = parent::TAB1.parent::VAR.$objectName.parent::CALL_INSERT_END;

        $lines[] = "";
        $lines[] = parent::TAB1.$this->createConstructor($objectApprovalName, $entityApprovalName, $objectName);
        $lines[] = "";

        $lines[] = parent::TAB1.parent::VAR.$objectApprovalName.parent::CALL_INSERT_END;
        $lines[] = "}";
        return implode(parent::NEW_LINE, $lines);
    }
    
    /**
     * Create UPDATE section without approval and trash
     *
     * @param MagicObject $mainEntity
     * @param AppField[] $appFields
     * @param boolean $approvalRequired
     * @param MagicObject $approvalEntity
     * @return string
     */
    public function createUpdateApprovalSection($mainEntity, $appFields, $approvalRequired, $approvalEntity)    
    {
        $entityName = $mainEntity->getEntityName();
        $objectName = lcfirst($entityName);
        $entityApprovalName = $approvalEntity->getEntityName();
        $pkeyApprovalName = $approvalEntity->getPrimaryKey();

        $objectApprovalName = lcfirst($entityApprovalName);
        $upperWaitingFor = PicoStringUtil::upperCamelize($this->entityInfo->getWaitingFor());
        $lines = array();
        
        $lines[] = "if(".parent::VAR."inputGet->getUserAction() == UserAction::UPDATE)";
        $lines[] = "{";
        $lines[] = parent::TAB1.$this->createConstructor($objectName, $entityName);
        $lines[] = "";
        $lines[] = parent::TAB1.$this->createConstructor($objectApprovalName, $entityApprovalName);
        foreach($appFields as $field)
        {
            $line = $this->createSetter($objectName, $field->getFieldName(), $field->getInputFilter());
            if($line != null)
            {
                $lines[] = $line;
            }
        }

        $upperAdminEdit = PicoStringUtil::upperCamelize($this->entityInfo->getAdminEdit());
        $upperTimeEdit = PicoStringUtil::upperCamelize($this->entityInfo->getTimeEdit());
        $upperIpEdit = PicoStringUtil::upperCamelize($this->entityInfo->getIpEdit());


        $lines[] = parent::TAB1.parent::VAR.$objectApprovalName.parent::CALL_SET.$upperAdminEdit."(".parent::VAR.$this->getCurrentAction()->getUserFunction().");";
        $lines[] = parent::TAB1.parent::VAR.$objectApprovalName.parent::CALL_SET.$upperTimeEdit."(".parent::VAR.$this->getCurrentAction()->getTimeFunction().");";
        $lines[] = parent::TAB1.parent::VAR.$objectApprovalName.parent::CALL_SET.$upperIpEdit."(".parent::VAR.$this->getCurrentAction()->getIpFunction().");";


        $lines[] = "";
        $lines[] = parent::TAB1.parent::VAR.$objectApprovalName.parent::CALL_INSERT_END;

        $lines[] = "";

        $upperAdminAskEdit = PicoStringUtil::upperCamelize($this->entityInfo->getAdminAskEdit());
        $upperTimeAskEdit = PicoStringUtil::upperCamelize($this->entityInfo->getTimeAskEdit());
        $upperIpAskEdit = PicoStringUtil::upperCamelize($this->entityInfo->getIpAskEdit());
        $upperPkeyApprovalName = PicoStringUtil::upperCamelize($pkeyApprovalName);

        $lines[] = parent::TAB1.parent::VAR.$objectName.parent::CALL_SET.$upperAdminAskEdit."(".parent::VAR.$this->getCurrentAction()->getUserFunction().");";
        $lines[] = parent::TAB1.parent::VAR.$objectName.parent::CALL_SET.$upperTimeAskEdit."(".parent::VAR.$this->getCurrentAction()->getTimeFunction().");";
        $lines[] = parent::TAB1.parent::VAR.$objectName.parent::CALL_SET.$upperIpAskEdit."(".parent::VAR.$this->getCurrentAction()->getIpFunction().");";

        $lines[] = "";
        $lines[] = parent::TAB1.parent::VAR.$objectName.parent::CALL_SET.$upperPkeyApprovalName."(".parent::VAR.$objectName."->get".$upperPkeyApprovalName."())->set".$upperWaitingFor."(".WaitingFor::ACTIVATE.")->update();";

        $lines[] = "}";
        return implode(parent::NEW_LINE, $lines);
    }
    
    /**
     * Create UPDATE section without approval and trash
     *
     * @param AppField[] $appFields
     * @param string $entityName
     * @param string $pkName
     * @param mixed $pkValue
     * @return string
     */
    public function createDeleteApprovalSectionBase($entityName, $pkName, $userAction, $waitingForKey, $waitingForFalue)
    {
        $objectName = lcfirst($entityName);
        $lines = array();
        $upperPkName = PicoStringUtil::upperCamelize($pkName);
        $upperWaitingFor = PicoStringUtil::upperCamelize($waitingForKey);
         
        $lines[] = "if(".parent::VAR."inputGet->getUserAction() == $userAction)";
        $lines[] = "{";
        $lines[] = parent::TAB1."if(".parent::VAR."inputPost->countableDeletionRowIds())";
        $lines[] = parent::TAB1."{";
        $lines[] = parent::TAB1.parent::TAB1."foreach(".parent::VAR."inputPost->getDeletionRowIds() as ".parent::VAR."rowId)";    
        $lines[] = parent::TAB1.parent::TAB1."{";
        $lines[] = parent::TAB1.parent::TAB1.parent::TAB1.$this->createConstructor($objectName, $entityName);
        $lines[] = parent::TAB1.parent::TAB1.parent::TAB1.parent::VAR.$objectName.parent::CALL_SET.$upperPkName."(".parent::VAR."rowId)->set".$upperWaitingFor."(".parent::VAR.$waitingForFalue.");";
        $lines[] = parent::TAB1.parent::TAB1.parent::TAB1.parent::VAR.$objectName.parent::CALL_UPDATE_END;
        $lines[] = parent::TAB1.parent::TAB1."}";
        $lines[] = parent::TAB1."}";
        $lines[] = "}";
        
        return implode(parent::NEW_LINE, $lines);
    }
    
    /**
     * Create delete approval section
     *
     * @param MagicObject $entityName
     * @return string
     */
    public function createDeleteApprovalSection($mainEntity)
    {
        $entityName = $mainEntity->getEntityName();
        $pkName =  $mainEntity->getPrimaryKey();
        $userAction = 'UserAction::DELETE';
        $waitingForFalue = WaitingFor::DELETE;
        return $this->createWaitingForSectionBase($entityName, $pkName, $userAction, $waitingForFalue);
    }
    
    /**
     * Create ACTIVATION section without approval and trash
     *
     * @param AppField[] $appFields
     * @param string $entityName
     * @param string $pkName
     * @param string $userAction
     * @param boolean $waitingForValue
     * @return string
     */
    public function createWaitingForSectionBase($entityName, $pkName, $userAction, $waitingForValue)
    {
        $objectName = lcfirst($entityName);
        $lines = array();
        $upperPkName = PicoStringUtil::upperCamelize($pkName);
        $upperWaitingFor = PicoStringUtil::upperCamelize($this->getentityInfo()->getWaitingFor());

        $upperAdminAskEdit = PicoStringUtil::upperCamelize($this->entityInfo->getAdminAskEdit());
        $upperTimeAskEdit = PicoStringUtil::upperCamelize($this->entityInfo->getTimeAskEdit());
        $upperIpAskEdit = PicoStringUtil::upperCamelize($this->entityInfo->getIpAskEdit());


        $lines[] = "if(".parent::VAR."inputGet->getUserAction() == $userAction)";
        $lines[] = "{";
        $lines[] = parent::TAB1."if(".parent::VAR."inputPost->countableAtivationRowIds())";
        $lines[] = parent::TAB1."{";
        $lines[] = parent::TAB1.parent::TAB1."foreach(".parent::VAR."inputPost->getAtivationRowIds() as ".parent::VAR."rowId)";    
        $lines[] = parent::TAB1.parent::TAB1."{";
        $lines[] = parent::TAB1.parent::TAB1.parent::TAB1.$this->createConstructor($objectName, $entityName);
        $lines[] = "";
        $lines[] = parent::TAB1.parent::TAB1.parent::TAB1.parent::VAR.$objectName.parent::CALL_SET.$upperAdminAskEdit."(".parent::VAR.$this->getCurrentAction()->getUserFunction().");";
        $lines[] = parent::TAB1.parent::TAB1.parent::TAB1.parent::VAR.$objectName.parent::CALL_SET.$upperTimeAskEdit."(".parent::VAR.$this->getCurrentAction()->getTimeFunction().");";
        $lines[] = parent::TAB1.parent::TAB1.parent::TAB1.parent::VAR.$objectName.parent::CALL_SET.$upperIpAskEdit."(".parent::VAR.$this->getCurrentAction()->getIpFunction().");";
        $lines[] = "";
        $lines[] = parent::TAB1.parent::TAB1.parent::TAB1.parent::VAR.$objectName.parent::CALL_SET.$upperPkName."(".parent::VAR."rowId)->set".$upperWaitingFor."(".$waitingForValue.")".parent::CALL_UPDATE_END;
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
     * @param string $pkName
     * @param string $activationKey
     * @param boolean $activationValue
     * @return string
     */
    public function createActivationApprovalSection($mainEntity)
    {
        $entityName = $mainEntity->getEntityName();
        $pkName =  $mainEntity->getPrimaryKey();
        $waitingForFalue = WaitingFor::ACTIVATE;
        $userAction = 'UserAction::ACTIVATE';
        return $this->createWaitingForSectionBase($entityName, $pkName, $userAction, $waitingForFalue);
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
    public function createDeactivationApprovalSection($mainEntity)
    {
        $entityName = $mainEntity->getEntityName();
        $pkName =  $mainEntity->getPrimaryKey();
        $waitingForFalue = WaitingFor::DEACTIVATE;
        $userAction = 'UserAction::DEACTIVATE';
        return $this->createWaitingForSectionBase($entityName, $pkName, $userAction, $waitingForFalue);
    }

    /**
     * Undocumented function
     *
     * @param MagicObject $mainEntity
     * @param AppField[] $editFields
     * @param boolean $approvalRequired
     * @param MagicObject $approvalEntity
     * @param boolean $trashRequired
     * @param MagicObject $trashEntity
     * @return string
     */
    public function createApprovalSection($mainEntity, $editFields, $approvalRequired, $approvalEntity, $trashRequired, $trashEntity)
    {
        $entityName = $mainEntity->getEntityName();
        $pkName =  $mainEntity->getPrimaryKey();
        
        $entityApprovalName = $approvalEntity->getEntityName();
        $entityTrashName = $trashEntity->getEntityName();
        
        $camelPkName = PicoStringUtil::camelize($pkName);
        $toBeCopied = array();
        foreach(array_keys($editFields) as $val)
        {
            $prop = PicoStringUtil::camelize($val);
            if(!in_array($val, $this->skipedAutoSetter) && $prop != $camelPkName)
            {
                $toBeCopied[] = $prop;
            }
        }
        $entityInfoName = "entityInfo";
        $entityApvInfoName = "entityApvInfo";
        $userAction = 'UserAction::APPROVE';
        $objectName = lcfirst($entityName);
        $lines = array();
        $upperPkName = PicoStringUtil::upperCamelize($pkName);
        $variableName = PicoStringUtil::camelize($pkName);

        $lines[] = "if(".parent::VAR."inputGet->getUserAction() == $userAction)";
        $lines[] = "{";
        $lines[] = parent::TAB1."if(".parent::VAR."inputPost->isset".$upperPkName."())";
        $lines[] = parent::TAB1."{";
        $lines[] = parent::TAB1.parent::TAB1.parent::VAR.$variableName." = ".parent::VAR."inputPost->get".$upperPkName."();";
    
        $lines[] = parent::TAB1.parent::TAB1.$this->createConstructor($objectName, $entityName);
        $lines[] = parent::TAB1.parent::TAB1.parent::VAR.$objectName."->findOneBy".$upperPkName."(".parent::VAR.$variableName.");";
        $lines[] = parent::TAB1.parent::TAB1."if(".parent::VAR.$objectName."->isset".$upperPkName."())";
        $lines[] = parent::TAB1.parent::TAB1."{";

        $lines[] = $this->constructApproval($objectName, $entityInfoName, $entityApvInfoName);
        $lines[] = "";
        
        $lines[] = parent::TAB1.parent::TAB1.parent::TAB1.parent::VAR."approvalCallback = new SetterGetter();";
        
        $lines[] = parent::TAB1.parent::TAB1.parent::TAB1.parent::VAR."approvalCallback->setAfterInsert(function("
        .parent::VAR."param1, ".parent::VAR."param2, ".parent::VAR."param3){".parent::NEW_LINE //NOSONAR
        .parent::TAB1.parent::TAB1.parent::TAB1.parent::TAB1."// callback on new data".parent::NEW_LINE //NOSONAR
        .parent::TAB1.parent::TAB1.parent::TAB1.parent::TAB1."// you code here".parent::NEW_LINE //NOSONAR
        .parent::TAB1.parent::TAB1.parent::TAB1.parent::TAB1."".parent::NEW_LINE //NOSONAR
        .parent::TAB1.parent::TAB1.parent::TAB1.parent::TAB1."return true;".parent::NEW_LINE //NOSONAR
        .parent::TAB1.parent::TAB1.parent::TAB1."}); ".parent::NEW_LINE; //NOSONAR


        $lines[] = parent::TAB1.parent::TAB1.parent::TAB1.parent::VAR."approvalCallback->setBeforeUpdate(function("
        .parent::VAR."param1, ".parent::VAR."param2, ".parent::VAR."param3){".parent::NEW_LINE //NOSONAR
        .parent::TAB1.parent::TAB1.parent::TAB1.parent::TAB1."// callback before update data".parent::NEW_LINE //NOSONAR
        .parent::TAB1.parent::TAB1.parent::TAB1.parent::TAB1."// you code here".parent::NEW_LINE //NOSONAR
        .parent::TAB1.parent::TAB1.parent::TAB1.parent::TAB1."".parent::NEW_LINE //NOSONAR
        
        .parent::TAB1.parent::TAB1.parent::TAB1."}); ".parent::NEW_LINE; //NOSONAR


        $lines[] = parent::TAB1.parent::TAB1.parent::TAB1.parent::VAR."approvalCallback->setAfterUpdate(function("
        .parent::VAR."param1, ".parent::VAR."param2, ".parent::VAR."param3){".parent::NEW_LINE //NOSONAR
        .parent::TAB1.parent::TAB1.parent::TAB1.parent::TAB1."// callback after update data".parent::NEW_LINE //NOSONAR
        .parent::TAB1.parent::TAB1.parent::TAB1.parent::TAB1."// you code here".parent::NEW_LINE //NOSONAR
        .parent::TAB1.parent::TAB1.parent::TAB1.parent::TAB1."".parent::NEW_LINE //NOSONAR
        
        .parent::TAB1.parent::TAB1.parent::TAB1."}); ".parent::NEW_LINE; //NOSONAR


        $lines[] = parent::TAB1.parent::TAB1.parent::TAB1.parent::VAR."approvalCallback->setAfterActivate(function("
        .parent::VAR."param1, ".parent::VAR."param2, ".parent::VAR."param3){".parent::NEW_LINE //NOSONAR
        .parent::TAB1.parent::TAB1.parent::TAB1.parent::TAB1."// callback after activate data".parent::NEW_LINE //NOSONAR
        .parent::TAB1.parent::TAB1.parent::TAB1.parent::TAB1."// you code here".parent::NEW_LINE //NOSONAR
        .parent::TAB1.parent::TAB1.parent::TAB1.parent::TAB1."".parent::NEW_LINE //NOSONAR
        
        .parent::TAB1.parent::TAB1.parent::TAB1."}); ".parent::NEW_LINE; //NOSONAR


        $lines[] = parent::TAB1.parent::TAB1.parent::TAB1.parent::VAR."approvalCallback->setAfterDeactivate(function("
        .parent::VAR."param1, ".parent::VAR."param2, ".parent::VAR."param3){".parent::NEW_LINE //NOSONAR
        .parent::TAB1.parent::TAB1.parent::TAB1.parent::TAB1."// callback after deactivate data".parent::NEW_LINE //NOSONAR
        .parent::TAB1.parent::TAB1.parent::TAB1.parent::TAB1."// you code here".parent::NEW_LINE //NOSONAR
        .parent::TAB1.parent::TAB1.parent::TAB1.parent::TAB1."".parent::NEW_LINE //NOSONAR
        
        .parent::TAB1.parent::TAB1.parent::TAB1."}); ".parent::NEW_LINE; //NOSONAR


        $lines[] = parent::TAB1.parent::TAB1.parent::TAB1.parent::VAR."approvalCallback->setBeforeDelete(function("
        .parent::VAR."param1, ".parent::VAR."param2, ".parent::VAR."param3){".parent::NEW_LINE //NOSONAR
        .parent::TAB1.parent::TAB1.parent::TAB1.parent::TAB1."// callback before delete data".parent::NEW_LINE //NOSONAR
        .parent::TAB1.parent::TAB1.parent::TAB1.parent::TAB1."// you code here".parent::NEW_LINE //NOSONAR
        .parent::TAB1.parent::TAB1.parent::TAB1.parent::TAB1."".parent::NEW_LINE //NOSONAR
        
        .parent::TAB1.parent::TAB1.parent::TAB1."}); ".parent::NEW_LINE; //NOSONAR


        $lines[] = parent::TAB1.parent::TAB1.parent::TAB1.parent::VAR."approvalCallback->setAfterDelete(function("
        .parent::VAR."param1, ".parent::VAR."param2, ".parent::VAR."param3){".parent::NEW_LINE //NOSONAR
        .parent::TAB1.parent::TAB1.parent::TAB1.parent::TAB1."// callback after delete data".parent::NEW_LINE //NOSONAR
        .parent::TAB1.parent::TAB1.parent::TAB1.parent::TAB1."// you code here".parent::NEW_LINE //NOSONAR
        .parent::TAB1.parent::TAB1.parent::TAB1.parent::TAB1."".parent::NEW_LINE //NOSONAR
        
        .parent::TAB1.parent::TAB1.parent::TAB1."}); ".parent::NEW_LINE; //NOSONAR

        $lines[] = parent::TAB1.parent::TAB1.parent::TAB1."// List of properties to be copied from $entityApprovalName to $entityName when user approve data modification. You can add or remove it".parent::NEW_LINE
        .parent::TAB1.parent::TAB1.parent::TAB1.parent::VAR."columToBeCopied = array(".parent::NEW_LINE
        .parent::TAB1.parent::TAB1.parent::TAB1.parent::TAB1.'"'.implode('", '.parent::NEW_LINE.parent::TAB1.parent::TAB1.parent::TAB1.parent::TAB1.'"', $toBeCopied).'"'.parent::NEW_LINE
        .parent::TAB1.parent::TAB1.parent::TAB1.");";
        $lines[] = "";

        $lines[] = parent::TAB1.parent::TAB1.parent::TAB1.parent::VAR."approval->approve("
        .parent::VAR."columToBeCopied, new $entityApprovalName(), new $entityTrashName(), ".parent::VAR."approvalCallback);";                                               
 

        $lines[] = parent::TAB1.parent::TAB1."}";

        $lines[] = parent::TAB1."}";
        $lines[] = "}";
        return implode(parent::NEW_LINE, $lines);
        
    }

    
    /**
     * Undocumented function
     *
     * @param MagicObject $mainEntity
     * @param boolean $approvalRequired
     * @param MagicObject $approvalEntity
     * @return string
     */
    public function createRejectionSection($mainEntity, $approvalRequired, $approvalEntity)
    {
        $entityName = $mainEntity->getEntityName();
        $entityApprovalName = $approvalEntity->getEntityName();
        $pkName =  $mainEntity->getPrimaryKey();
        $entityInfoName = "entityInfo";
        $entityApvInfoName = "entityApvInfo";
        $userAction = 'UserAction::REJECT';
        $objectName = lcfirst($entityName);
        $lines = array();
        $upperPkName = PicoStringUtil::upperCamelize($pkName);
        $variableName = PicoStringUtil::camelize($pkName);

        $lines[] = "if(".parent::VAR."inputGet->getUserAction() == $userAction)";
        $lines[] = "{";
        $lines[] = parent::TAB1."if(".parent::VAR."inputPost->isset".$upperPkName."())";
        $lines[] = parent::TAB1."{";
        $lines[] = parent::TAB1.parent::TAB1.parent::VAR.$variableName." = ".parent::VAR."inputPost->get".$upperPkName."();";
    
        $lines[] = parent::TAB1.parent::TAB1.$this->createConstructor($objectName, $entityName);
        $lines[] = parent::TAB1.parent::TAB1.parent::VAR.$objectName."->findOneBy".$upperPkName."(".parent::VAR.$variableName.");";
        $lines[] = parent::TAB1.parent::TAB1."if(".parent::VAR.$objectName."->isset".$upperPkName."())";
        $lines[] = parent::TAB1.parent::TAB1."{";

        $lines[] = $this->constructApproval($objectName, $entityInfoName, $entityApvInfoName);
        $lines[] = parent::TAB1.parent::TAB1.parent::TAB1.parent::VAR."approval->reject(new $entityApprovalName());";


        $lines[] = parent::TAB1.parent::TAB1."}";

        $lines[] = parent::TAB1."}";
        $lines[] = "}";
        return implode(parent::NEW_LINE, $lines);

    }

    protected function constructApproval($objectName, $entityInfoName, $entityApvInfoName)
    {
        return parent::TAB1.parent::TAB1.parent::TAB1.parent::VAR."approval = new PicoApproval(".parent::VAR.$objectName.", "
        .parent::VAR.$entityInfoName.", ".parent::VAR.$entityApvInfoName.", "
        .parent::NEW_LINE //NOSONAR
        .parent::TAB1.parent::TAB1.parent::TAB1."function(".parent::VAR."param1, ".parent::VAR."param2, ".parent::VAR."param3){".parent::NEW_LINE //NOSONAR
        .parent::TAB1.parent::TAB1.parent::TAB1.parent::TAB1."// approval validation here".parent::NEW_LINE //NOSONAR
        .parent::TAB1.parent::TAB1.parent::TAB1.parent::TAB1."// if return false, approval can not be done".parent::NEW_LINE //NOSONAR
        .parent::TAB1.parent::TAB1.parent::TAB1.parent::TAB1."".parent::NEW_LINE //NOSONAR
        .parent::TAB1.parent::TAB1.parent::TAB1.parent::TAB1."return true;".parent::NEW_LINE //NOSONAR
        .parent::TAB1.parent::TAB1.parent::TAB1."}, ".parent::NEW_LINE //NOSONAR
        .parent::TAB1.parent::TAB1.parent::TAB1."function(".parent::VAR."param1, ".parent::VAR."param2, ".parent::VAR."param3){".parent::NEW_LINE //NOSONAR
        .parent::TAB1.parent::TAB1.parent::TAB1.parent::TAB1."// callback when success".parent::NEW_LINE //NOSONAR
        .parent::TAB1.parent::TAB1.parent::TAB1."}, ".parent::NEW_LINE //NOSONAR
        .parent::TAB1.parent::TAB1.parent::TAB1."function(".parent::VAR."param1, ".parent::VAR."param2, ".parent::VAR."param3){".parent::NEW_LINE //NOSONAR
        .parent::TAB1.parent::TAB1.parent::TAB1.parent::TAB1."// callback when failed".parent::NEW_LINE //NOSONAR
        .parent::TAB1.parent::TAB1.parent::TAB1."} ".parent::NEW_LINE //NOSONAR
        .parent::TAB1.parent::TAB1.parent::TAB1.");"; //NOSONAR
    }
}