<?php

namespace AppBuilder;

use MagicObject\MagicObject;

class AppFeatures
{
    private $activateDeactivate = false;
    private $sortOrder = false;
    private $approvalRequired = false;
    private $approvalNote = false;
    private $trashRequired = false;
    
    /**
     * Constructor
     *
     * @param MagicObject $features
     */
    public function __construct($features)
    {
        if($features != null)
        {
            $this->activateDeactivate = $this->isTrue($features->get('activateDeactivate'));
            $this->sortOrder = $this->isTrue($features->get('sortOrder'));
            $this->approvalRequired = $this->isTrue($features->get('approvalRequired'));
            $this->approvalNote = $this->isTrue($features->get('approvalNote'));
            $this->trashRequired = $this->isTrue($features->get('trashRequired'));
        }
    }
    
    /**
     * Check if value is true
     *
     * @param mixed $value
     * @return boolean
     */
    private function isTrue($value)
    {
        return $value == '1' || strtolower($value) == 'true' || $value === 1 || $value === true;
    }

    /**
     * Get the value of activateDeactivate
     */ 
    public function isActiavteDeactivate()
    {
        return $this->activateDeactivate == 1;
    }

    /**
     * Get the value of sortOrder
     */ 
    public function isSortOrder()
    {
        return $this->sortOrder == 1;
    }

    /**
     * Get the value of approvalRequired
     */ 
    public function isApprovalRequired()
    {
        return $this->approvalRequired == 1;
    }

    /**
     * Get the value of approvalNote
     */ 
    public function isApprovalNote()
    {
        return $this->approvalNote == 1;
    }

    /**
     * Get the value of trashRequired
     */ 
    public function isTrashRequired()
    {
        return $this->trashRequired == 1;
    }
}