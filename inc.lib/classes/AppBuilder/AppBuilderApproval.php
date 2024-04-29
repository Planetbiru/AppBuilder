<?php

namespace AppBuilder;

use AppBuilder\Base\AppBuilderBase;
use MagicObject\Util\PicoStringUtil;

class AppBuilderApproval extends AppBuilderBase
{
    /**
     * Create INSERT section without approval and trash
     *
     * @param AppField[] $appFields
     * @param string $entityName
     * @param string $pkeyName
     * @param string $entityNameApproval
     * @return string
     */
    public function createInsertApprovalSection($entityName, $appFields, $pkeyName, $entityNameApproval)
    {
        $objectName = lcfirst($entityName);
        $objectNameApproval = lcfirst($entityNameApproval);
        $upperWaitingFor = PicoStringUtil::upperCamelize($this->entitiInfo->getWaitingFor());
        $upperDraft = PicoStringUtil::upperCamelize($this->entitiInfo->getDraft());



        $lines = array();


        
        $lines[] = "if(".parent::VAR."inputGet->getUserAction() == UserAction::INSERT)";
        $lines[] = "{";
        $lines[] = parent::TAB1.$this->createConstructor($objectName, $entityName);
        foreach($appFields as $field)
        {
            $fieldName = $field->getName();
            $fieldFilter = $field->getFilter();
            $line = $this->createSetter($objectName, $fieldName, $fieldFilter);
            if($line != null)
            {
                $lines[] = $line;
            }
        }
        
        // set draft
        $lines[] = parent::TAB1.parent::VAR.$objectName."->set".$upperDraft."(true);";
        $lines[] = parent::TAB1.parent::VAR.$objectName."->set".$upperWaitingFor."(".WaitingFor::CREATE.");";

        $upperAdminCreate = PicoStringUtil::upperCamelize($this->entitiInfo->getAdminCreate());
        $upperTimeCreate = PicoStringUtil::upperCamelize($this->entitiInfo->getTimeCreate());
        $upperIpCreate = PicoStringUtil::upperCamelize($this->entitiInfo->getIpCreate());

        $upperAdminEdit = PicoStringUtil::upperCamelize($this->entitiInfo->getAdminEdit());
        $upperTimeEdit = PicoStringUtil::upperCamelize($this->entitiInfo->getTimeEdit());
        $upperIpEdit = PicoStringUtil::upperCamelize($this->entitiInfo->getIpEdit());

        $lines[] = parent::TAB1.parent::VAR.$objectName.parent::CALL_SET.$upperAdminCreate."(".parent::VAR.$this->getCurrentAction()->getUserFunction().");";
        $lines[] = parent::TAB1.parent::VAR.$objectName.parent::CALL_SET.$upperTimeCreate."(".parent::VAR.$this->getCurrentAction()->getTimeFunction().");";
        $lines[] = parent::TAB1.parent::VAR.$objectName.parent::CALL_SET.$upperIpCreate."(".parent::VAR.$this->getCurrentAction()->getIpFunction().");";

        $lines[] = parent::TAB1.parent::VAR.$objectName.parent::CALL_SET.$upperAdminEdit."(".parent::VAR.$this->getCurrentAction()->getUserFunction().");";
        $lines[] = parent::TAB1.parent::VAR.$objectName.parent::CALL_SET.$upperTimeEdit."(".parent::VAR.$this->getCurrentAction()->getTimeFunction().");";
        $lines[] = parent::TAB1.parent::VAR.$objectName.parent::CALL_SET.$upperIpEdit."(".parent::VAR.$this->getCurrentAction()->getIpFunction().");";

        $lines[] = "";

        $lines[] = parent::TAB1.parent::VAR.$objectName.parent::CALL_INSERT_END;

        $lines[] = "";
        $lines[] = parent::TAB1.$this->createConstructor($objectNameApproval, $entityNameApproval, $objectName);
        $lines[] = "";

        $lines[] = parent::TAB1.parent::VAR.$objectNameApproval.parent::CALL_INSERT_END;
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
    public function createUpdateApprovalSection($entityName, $appFields, $pkeyName, $entityNameApproval, $pkeyApprovalName)
    {
        $objectName = lcfirst($entityName);
        $objectNameApproval = lcfirst($entityNameApproval);
        $upperWaitingFor = PicoStringUtil::upperCamelize($this->entitiInfo->getWaitingFor());
        $lines = array();
        
        $lines[] = "if(".parent::VAR."inputGet->getUserAction() == UserAction::UPDATE)";
        $lines[] = "{";
        $lines[] = parent::TAB1.$this->createConstructor($objectName, $entityName);
        $lines[] = "";
        $lines[] = parent::TAB1.$this->createConstructor($objectNameApproval, $entityNameApproval);
        foreach($appFields as $field)
        {
            $fieldName = $field->getName();
            $fieldFilter = $field->getFilter();
            $line = $this->createSetter($objectNameApproval, $fieldName, $fieldFilter);
            if($line != null)
            {
                $lines[] = $line;
            }
        }

        $upperAdminEdit = PicoStringUtil::upperCamelize($this->entitiInfo->getAdminEdit());
        $upperTimeEdit = PicoStringUtil::upperCamelize($this->entitiInfo->getTimeEdit());
        $upperIpEdit = PicoStringUtil::upperCamelize($this->entitiInfo->getIpEdit());


        $lines[] = parent::TAB1.parent::VAR.$objectNameApproval.parent::CALL_SET.$upperAdminEdit."(".parent::VAR.$this->getCurrentAction()->getUserFunction().");";
        $lines[] = parent::TAB1.parent::VAR.$objectNameApproval.parent::CALL_SET.$upperTimeEdit."(".parent::VAR.$this->getCurrentAction()->getTimeFunction().");";
        $lines[] = parent::TAB1.parent::VAR.$objectNameApproval.parent::CALL_SET.$upperIpEdit."(".parent::VAR.$this->getCurrentAction()->getIpFunction().");";


        $lines[] = "";
        $lines[] = parent::TAB1.parent::VAR.$objectNameApproval.parent::CALL_INSERT_END;

        $lines[] = "";

        $upperAdminAskEdit = PicoStringUtil::upperCamelize($this->entitiInfo->getAdminAskEdit());
        $upperTimeAskEdit = PicoStringUtil::upperCamelize($this->entitiInfo->getTimeAskEdit());
        $upperIpAskEdit = PicoStringUtil::upperCamelize($this->entitiInfo->getIpAskEdit());
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
    
    public function createDeleteApprovalSection($entityName, $pkName)
    {
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
        $upperWaitingFor = PicoStringUtil::upperCamelize($this->getEntitiInfo()->getWaitingFor());

        $upperAdminAskEdit = PicoStringUtil::upperCamelize($this->entitiInfo->getAdminAskEdit());
        $upperTimeAskEdit = PicoStringUtil::upperCamelize($this->entitiInfo->getTimeAskEdit());
        $upperIpAskEdit = PicoStringUtil::upperCamelize($this->entitiInfo->getIpAskEdit());


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
     * @param string $entityName
     * @param string $pkName
     * @param string $activationKey
     * @param boolean $activationValue
     * @return string
     */
    public function createActivationApprovalSection($entityName, $pkName)
    {
        $waitingForFalue = WaitingFor::ACTIVATE;
        $userAction = 'UserAction::ACTIVATE';
        return $this->createWaitingForSectionBase($entityName, $pkName, $userAction, $waitingForFalue);
    }
    
    /**
     * Create DEACTIVATION section without approval and trash
     *
     * @param AppField[] $appFields
     * @param string $entityName
     * @param string $pkName
     * @param string $activationKey
     * @param boolean $activationValue
     * @return string
     */
    public function createDeactivationApprovalSection($entityName, $pkName)
    {
        $waitingForFalue = WaitingFor::DEACTIVATE;
        $userAction = 'UserAction::DEACTIVATE';
        return $this->createWaitingForSectionBase($entityName, $pkName, $userAction, $waitingForFalue);
    }

    /**
     * Undocumented function
     *
     * @param string $entityName
     * @param string $pkName
     * @param array $editFields
     * @return string
     */
    public function createApprovalSection($entityName, $pkName, $editFields, $entityNameApproval, $entityNameTrash)
    {
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

        $lines[] = $this->constructApproval($objectName, $entityInfoName);
        $lines[] = "";
        $lines[] = parent::TAB1.parent::TAB1.parent::TAB1."// List of properties to be copied from $entityNameApproval to $entityName. You can add or remove it".parent::NEW_LINE
        .parent::TAB1.parent::TAB1.parent::TAB1.parent::VAR."columToBeCopied = array(".parent::NEW_LINE
        .parent::TAB1.parent::TAB1.parent::TAB1.parent::TAB1.'"'.implode('", '.parent::NEW_LINE.parent::TAB1.parent::TAB1.parent::TAB1.parent::TAB1.'"', $toBeCopied).'"'.parent::NEW_LINE
        .parent::TAB1.parent::TAB1.parent::TAB1.");";
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


        $lines[] = parent::TAB1.parent::TAB1.parent::TAB1.parent::VAR."approval->approve("
        .parent::VAR."columToBeCopied---, new $entityNameApproval(), new $entityNameTrash());";                                               
 

        $lines[] = parent::TAB1.parent::TAB1."}";

        $lines[] = parent::TAB1."}";
        $lines[] = "}";
        return implode(parent::NEW_LINE, $lines);
        
    }

    public function createRejectionSection($entityName, $pkName, $entityNameApproval)
    {
        $entityInfoName = "entityInfo";
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

        $lines[] = $this->constructApproval($objectName, $entityInfoName);
        $lines[] = parent::TAB1.parent::TAB1.parent::TAB1.parent::VAR."approval->reject(new $entityNameApproval());";


        $lines[] = parent::TAB1.parent::TAB1."}";

        $lines[] = parent::TAB1."}";
        $lines[] = "}";
        return implode(parent::NEW_LINE, $lines);

    }

    protected function constructApproval($objectName, $entityInfoName)
    {
        $upperWaitingFor = PicoStringUtil::upperCamelize($this->entitiInfo->getWaitingFor());
        $upperDraft = PicoStringUtil::upperCamelize($this->entitiInfo->getDraft());
        return parent::TAB1.parent::TAB1.parent::TAB1.parent::VAR."approval = new PicoApproval(".parent::VAR.$objectName.", "
        .parent::VAR.$entityInfoName.", "
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