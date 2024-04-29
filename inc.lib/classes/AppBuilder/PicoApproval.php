<?php

namespace AppBuilder;

use Exception;
use MagicObject\MagicObject;
use MagicObject\SetterGetter;

class PicoApproval
{
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
    private $callbackAnApprove = null;
    
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
     * @param callable $callbackValidation
     * @param callable $callbackAnApprove
     * @param callable $callackOnReject
     */
    public function __construct($entity, $entityInfo, $callbackValidation = null, $callbackAnApprove = null, $callackOnReject = null)
    {
        $this->entity = $entity;
        $this->entityInfo = $entityInfo;
        $this->callbackValidation = $callbackValidation;
        $this->callbackAnApprove = $callbackAnApprove;
        $this->callackOnReject = $callackOnReject;
    }
    
    /**
     * Approve
     *
     * @param string[] $columToBeCopied
     * @param MagicObject $entityApv
     * @param MagicObject $entityTrash
     * @param SetterGetter $approvalCallback
     * @return void
     */
    public function approve($columToBeCopied, $entityApv, $entityTrash, $approvalCallback)
    {
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
            
            $this->entity->delete();
            
            if($approvalCallback != null && $approvalCallback->getAfterDelete() != null && is_callable($approvalCallback->getAfterDelete()))
            {
                call_user_func($approvalCallback->getAfterDelete(), $this->entity, null, null);
            }
        }
    }
    
    private function approveUpdate($entityApv, $columToBeCopied)
    {
        $approvalId = $this->entity->get($this->entityInfo->getApprovalId());
        if($approvalId != null)
        {
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
            }
            catch(Exception $e)
            {
                // do nothing
            }
        }
    }
    
    public function reject($entityApproval)
    {
        $waitingFor = $this->entity->get($this->entityInfo->getWaitingFor());
        if($waitingFor == WaitingFor::CREATE)
        {
            $this->entity->delete();
        }
        if($waitingFor == WaitingFor::UPDATE || $waitingFor == WaitingFor::ACTIVATE || $waitingFor == WaitingFor::DEACTIVATE || $waitingFor == WaitingFor::DELETE)
        {
            $this->entity->set($this->entityInfo->getWaitingFor(), WaitingFor::NOTHING)->update();
        }
    }
}