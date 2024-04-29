<?php

namespace AppBuilder;

use MagicObject\MagicObject;

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
    
    public function approve($entityApproval)
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