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
    private $callbackOnReject = null;
    
    /**
     * Constructor
     *
     * @param MagicObject $entity
     * @param EntityInfo $entityInfo
     * @param EntityApvInfo $entityApvInfo
     * @param callable $callbackValidation
     */
    public function __construct($entity, $entityInfo, $entityApvInfo, $callbackValidation = null)
    {
        $this->entity = $entity;
        $this->entityInfo = $entityInfo;
        $this->entityApvInfo = $entityApvInfo;
        $this->callbackValidation = $callbackValidation;
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
    public function approve($columToBeCopied, $entityApv, $entityTrash, $currentUser, $currentTime, $currentIp, $approvalCallback = null)
    {
        $this->validateApproval();
        $waitingFor = $this->entity->get($this->entityInfo->getWaitingFor());
        if($waitingFor == WaitingFor::CREATE)
        {    
            $this->entity
                ->set($this->entityInfo->getWaitingFor(), WaitingFor::NOTHING)
                ->set($this->entityInfo->getDraft(), false)
                ->set($this->entityInfo->getApprovalId(), null)
                ->update();
        }
        else if($waitingFor == WaitingFor::ACTIVATE)
        {
            // copy into variables
            $adminEdit = $this->entity->get($this->entityInfo->getAdminAskEdit());
            $timeEdit = $this->entity->get($this->entityInfo->getTimeAskEdit());
            $ipEdit = $this->entity->get($this->entityInfo->getIpAskEdit());
            
            $this->entity
                ->set($this->entityInfo->getActive(), true)
                ->set($this->entityInfo->getWaitingFor(), WaitingFor::NOTHING)
                ->set($this->entityInfo->getAdminAskEdit(), $adminEdit)
                ->set($this->entityInfo->getTimeAskEdit(), $timeEdit)
                ->set($this->entityInfo->getIpAskEdit(), $ipEdit)
                ->update();
        }
        else if($waitingFor == WaitingFor::DEACTIVATE)
        {
            // copy into variables
            $adminEdit = $this->entity->get($this->entityInfo->getAdminAskEdit());
            $timeEdit = $this->entity->get($this->entityInfo->getTimeAskEdit());
            $ipEdit = $this->entity->get($this->entityInfo->getIpAskEdit());
            
            $this->entity
                ->set($this->entityInfo->getActive(), false)
                ->set($this->entityInfo->getWaitingFor(), WaitingFor::NOTHING)
                ->set($this->entityInfo->getAdminAskEdit(), $adminEdit)
                ->set($this->entityInfo->getTimeAskEdit(), $timeEdit)
                ->set($this->entityInfo->getIpAskEdit(), $ipEdit)
                ->update();
        }
        else if($waitingFor == WaitingFor::UPDATE)
        {
            $this->approveUpdate($entityApv, $columToBeCopied);
        }
        else if($waitingFor == WaitingFor::DELETE)
        {
            $this->approveDelete($entityTrash, $currentUser, $currentTime, $currentIp, $approvalCallback = null);
        }
        $this->callbackApprove();
        return $this;
    }
    
    public function approveDelete($entityTrash, $currentUser, $currentTime, $currentIp, $approvalCallback = null)
    {
        if($approvalCallback != null && $approvalCallback->getBeforeDelete() != null && is_callable($approvalCallback->getBeforeDelete()))
        {
            call_user_func($approvalCallback->getBeforeDelete(), $this->entity, null, null);
        }
        if($entityTrash != null)
        {
            // copy database connection from entity to entityTrash
            $entityTrash->currentDatabase($this->entity->currentDatabase());

            // copy data from entity to entityTrash
            $entityTrash->loadData($this->entity)->insert();
        }
        // delete data
        $this->entity->delete();
        
        if($approvalCallback != null && $approvalCallback->getAfterDelete() != null && is_callable($approvalCallback->getAfterDelete()))
        {
            call_user_func($approvalCallback->getAfterDelete(), $this->entity, null, null);
        }
    }
    
    /**
     * Reject
     *
     * @param MagicObject $entityApv
     * @return self
     */
    public function reject($entityApv, $currentUser, $currentTime, $currentIp)
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
        $this->callbackReject();
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
     * Callback approval
     *
     * @return boolean
     */
    private function callbackApprove()
    {
        if($this->callbackOnApprove != null && is_callable($this->callbackOnApprove))
        {
            return call_user_func($this->callbackOnApprove, $this->entity, null, null);
        }
        return true;
    }
    
    /**
     * Callback approval
     *
     * @return boolean
     */
    private function callbackReject()
    {
        if($this->callbackOnReject != null && is_callable($this->callbackOnReject))
        {
            return call_user_func($this->callbackOnReject, $this->entity, null, null);
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
                
                $adminEdit = $this->entity->get($this->entityInfo->getAdminAskEdit());
                $timeEdit = $this->entity->get($this->entityInfo->getTimeAskEdit());
                $ipEdit = $this->entity->get($this->entityInfo->getIpAskEdit());
                
                $this->entity
                    ->set($this->entityInfo->getWaitingFor(), WaitingFor::NOTHING)
                    ->set($this->entityInfo->getAdminAskEdit(), $adminEdit)
                    ->set($this->entityInfo->getTimeAskEdit(), $timeEdit)
                    ->set($this->entityInfo->getIpAskEdit(), $ipEdit);
                
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