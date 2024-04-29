<?php

namespace AppBuilder;

use Exception;
use MagicObject\MagicObject;
use MagicObject\SetterGetter;

class PicoApproval
{
    const APPROVAL_APPROVE = 1;
    const APPROVAL_REJECT = 2;
    /**
     * Master entity
     *
     * @var MagicObject
     */
    private $entity = null;
    
    /**
     * Entity info
     *
     * @var EntityInfo
     */
    private $entityInfo;
   
    /**
     * Entity approval info
     *
     * @var EntityApvInfo
     */
    private $entityApvInfo;

    /**
     * Callback validation
     *
     * @var callable
     */
    private $callbackValidation = null;
    
    /**
     * Callback on approve
     *
     * @var callable
     */
    private $callbackOnApprove = null;
    
    /**
     * Callback on reject
     *
     * @var callable
     */
    private $callackOnReject = null;
    
    /**
     * Constructor
     *
     * @param MagicObject $entity
     * @param EntityInfo $entityInfo
     * @param EntityApvInfo $entityApvInfo
     * @param callable $callbackValidation
     * @param callable $callbackOnApprove
     * @param callable $callackOnReject
     */
    public function __construct($entity, $entityInfo, $entityApvInfo, $callbackValidation = null, $callbackOnApprove = null, $callackOnReject = null)
    {
        $this->entity = $entity;
        $this->entityInfo = $entityInfo;
        $this->entityApvInfo = $entityApvInfo;
        $this->callbackValidation = $callbackValidation;
        $this->callbackOnApprove = $callbackOnApprove;
        $this->callackOnReject = $callackOnReject;
    }
    
    /**
     * Approve
     *
     * @param string[] $columToBeCopied
     * @param MagicObject $entityApv
     * @param MagicObject $entityTrash
     * @param SetterGetter $approvalCallback
     * @return self
     */
    public function approve($columToBeCopied, $entityApv, $entityTrash, $approvalCallback)
    {
        $this->validateApproval();
        $waitingFor = $this->entity->get($this->entityInfo->getWaitingFor());
        if($waitingFor == WaitingFor::CREATE)
        {
            
            $this->entity->set($this->entityInfo->getWaitingFor(), WaitingFor::NOTHING)->set($this->entityInfo->getDraft(), false)->update();
        }
        if($waitingFor == WaitingFor::ACTIVATE)
        {
            $this->entity->set($this->entityInfo->getWaitingFor(), WaitingFor::NOTHING)->set($this->entityInfo->getActive(), true)->update();
        }
        if($waitingFor == WaitingFor::DEACTIVATE)
        {
            $this->entity->set($this->entityInfo->getWaitingFor(), WaitingFor::NOTHING)->set($this->entityInfo->getActive(), false)->update();
        }
        if($waitingFor == WaitingFor::UPDATE)
        {
            $this->approveUpdate($entityApv, $columToBeCopied);
        }
        if($waitingFor == WaitingFor::DELETE)
        {
            if($approvalCallback != null && $approvalCallback->getBeforeDelete() != null && is_callable($approvalCallback->getBeforeDelete()))
            {
                call_user_func($approvalCallback->getBeforeDelete(), $this->entity, null, null);
            }
            if($entityTrash != null)
            {
                // copy database connection from entity to entityApv
                $entityTrash->currentDatabase($this->entity->currentDatabase());

                // copy data from entity to entityApv
                $entityTrash->loadData($this->entity)->insert();
            }
            $this->entity->delete();
            
            if($approvalCallback != null && $approvalCallback->getAfterDelete() != null && is_callable($approvalCallback->getAfterDelete()))
            {
                call_user_func($approvalCallback->getAfterDelete(), $this->entity, null, null);
            }
        }
        return $this;
    }
    
    /**
     * Reject
     *
     * @param MagicObject $entityApv
     * @return self
     */
    public function reject($entityApv)
    {
        $this->validateApproval();
        $waitingFor = $this->entity->get($this->entityInfo->getWaitingFor());
        $entityApv->currentDatabase($this->entity->currentDatabase());
        if($waitingFor == WaitingFor::CREATE)
        {
            $entityApv->set($this->entityApvInfo->getApprovalStatus(), self::APPROVAL_REJECT)->update();
            $this->entity->delete();
        }
        else if($waitingFor == WaitingFor::UPDATE || $waitingFor == WaitingFor::ACTIVATE || $waitingFor == WaitingFor::DEACTIVATE || $waitingFor == WaitingFor::DELETE)
        {
            $entityApv->set($this->entityApvInfo->getApprovalStatus(), self::APPROVAL_REJECT)->update();
            $this->entity->set($this->entityInfo->getWaitingFor(), WaitingFor::NOTHING)->update();
        }
        return $this;
    }
    
    /**
     * Validate approval
     *
     * @return boolean
     */
    private function validateApproval()
    {
        if($this->callbackValidation != null && is_callable($this->callbackValidation))
        {
            return call_user_func($this->callbackValidation, $this->entity, null, null);
        }
        return true;
    }
    
    /**
     * Approve update
     *
     * @param MagicObject $entityApv
     * @param string[] $columToBeCopied
     * @return self
     */
    private function approveUpdate($entityApv, $columToBeCopied)
    {
        $approvalId = $this->entity->get($this->entityInfo->getApprovalId());
        if($approvalId != null)
        {
            // copy database connection from entity to entityApv
            $entityApv->currentDatabase($this->entity->currentDatabase());
            try
            {
                $entityApv->find($approvalId);
                $values = $entityApv->valueArray();
                $updated = 0;
                foreach($values as $field=>$value)
                {
                    if(in_array($field, $columToBeCopied))
                    {
                        $this->entity->set($field, $value);
                        $updated++;
                    }
                }
                if($updated > 0)
                {
                    $this->entity->update();
                }
                $entityApv->set($this->entityApvInfo->getApprovalStatus(), self::APPROVAL_REJECT)->update();
            }
            catch(Exception $e)
            {
                // do nothing
            }
        }
        return $this;
    }
    
    
}