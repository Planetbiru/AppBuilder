<?php

namespace AppBuilder;

use MagicObject\Util\PicoGenericObject;

class AppField
{
    public $name;
    public $dataType;
    public $elementType;
    public $filter;
    public $fieldLabel;
    public function __construct($value)
    {
        $json = new PicoGenericObject($value);
        $this->name = $json->getFieldName();
        $this->dataType = $json->getDataType();
        $this->elementType = $json->getElementType();
        $this->filter = $json->getInputFilter();
        $this->fieldLabel = $json->getFieldLabel();
    }

    

    /**
     * Get the value of name
     */ 
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the value of dataType
     */ 
    public function getDataType()
    {
        return $this->dataType;
    }

    /**
     * Get the value of elementType
     */ 
    public function getElementType()
    {
        return $this->elementType;
    }

    /**
     * Get the value of filter
     */ 
    public function getFilter()
    {
        return $this->filter;
    }
}