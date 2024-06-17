<?php

namespace MagicApp;

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
    private $callbackAfterApprove = null;
    
    /**
     * Callback on reject
     *
     * @var callable
     */
    private $callbackAfterReject = null;

    /**
     * Current user
     *
     * @var string
     */
    private $currentUser;

    /**
     * Current time
     *
     * @var string
     */
    private $currentTime;

    /**
     * Current IP
     *
     * @var string
     */
    private $currentIp;
    
    /**
     * Constructor
     *
     * @param MagicObject $entity
     * @param EntityInfo $entityInfo
     * @param EntityApvInfo $entityApvInfo
     * @param string $currentUser
     * @param string $currentTime
     * @param string $currentIp
     * @param callable $callbackValidation
     * @param callable $callbackAfterApprove
     * @param callable $callbackAfterReject
     */
    public function __construct($entity, $entityInfo, $entityApvInfo, $callbackValidation = null, $useTrash = false, $entityTrash = null)
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
        if($approvalCallback != null && $approvalCallback->getAfterApprove() != null && is_callable($approvalCallback->getAfterApprove()))
        {
            call_user_func($approvalCallback->getAfterApprove(), $this->entity, null, null);
        }
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
    public function reject($entityApv, $currentUser = null, $currentTime = null, $currentIp = null, $approvalCallback = null)
    {
        if($approvalCallback != null && $approvalCallback->getBeforeReject() != null && is_callable($approvalCallback->getBeforeReject()))
        {
            call_user_func($approvalCallback->getBeforeReject(), $this->entity, null, null);
        }
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
        if($approvalCallback != null && $approvalCallback->getAfterReject() != null && is_callable($approvalCallback->getAfterReject()))
        {
            call_user_func($approvalCallback->getAfterReject(), $this->entity, null, null);
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
     * Callback approval
     *
     * @return boolean
     */
    private function callbackAfterApprove()
    {
        if($this->callbackAfterApprove != null && is_callable($this->callbackAfterApprove))
        {
            return call_user_func($this->callbackAfterApprove, $this->entity, null, null);
        }
        return true;
    }
    
    /**
     * Callback approval
     *
     * @return boolean
     */
    private function callbackAfterReject()
    {
        if($this->callbackAfterReject != null && is_callable($this->callbackAfterReject))
        {
            return call_user_func($this->callbackAfterReject, $this->entity, null, null);
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
    
    

    /**
     * Get current user
     *
     * @return  string
     */ 
    public function getCurrentUser()
    {
        return $this->currentUser;
    }

    /**
     * Get current time
     *
     * @return  string
     */ 
    public function getCurrentTime()
    {
        return $this->currentTime;
    }

    /**
     * Get current IP
     *
     * @return  string
     */ 
    public function getCurrentIp()
    {
        return $this->currentIp;
    }
}