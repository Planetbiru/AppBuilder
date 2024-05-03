<?php

namespace AppBuilder;

use MagicObject\MagicObject;
use MagicObject\Util\PicoGenericObject;

class AppFeatures
{
    private $actiavteDeactivate = false;
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
        $this->actiavteDeactivate = $this->isTrue($features->get('actiavteDeactivate'));
        $this->sortOrder = $this->isTrue($features->get('sortOrder'));
        $this->approvalRequired = $this->isTrue($features->get('approvalRequired'));
        $this->approvalNote = $this->isTrue($features->get('approvalNote'));
        $this->trashRequired = $this->isTrue($features->get('trashRequired'));
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
     * Get the value of actiavteDeactivate
     */ 
    public function isActiavteDeactivate()
    {
        return $this->actiavteDeactivate;
    }

    /**
     * Get the value of sortOrder
     */ 
    public function isSortOrder()
    {
        return $this->sortOrder;
    }

    /**
     * Get the value of approvalRequired
     */ 
    public function isApprovalRequired()
    {
        return $this->approvalRequired;
    }

    /**
     * Get the value of approvalNote
     */ 
    public function isApprovalNote()
    {
        return $this->approvalNote;
    }

    /**
     * Get the value of trashRequired
     */ 
    public function isTrashRequired()
    {
        return $this->trashRequired;
    }
}