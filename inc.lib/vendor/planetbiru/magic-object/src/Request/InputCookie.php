<?php

namespace MagicObject\Request;

use MagicObject\Util\ClassUtil\PicoObjectParser;

class  InputCookie extends PicoRequestBase {
    /**
     * Recursive
     *
     * @var boolean
     */
    private $_recursive = false;

    /**
     * Constructor
     * @param boolean $recursive
     */
    public function __construct($recursive = false)
    {
        parent::__construct();
        $this->_recursive = $recursive; 
        $this->loadData($_COOKIE);
    }

    /**
     * Get global variable $_COOKIE
     *
     * @return array
     */
    public static function requestCookie()
    {
        return $_COOKIE;
    }

    /**
     * Override loadData
     *
     * @param array $data
     * @return self
     */
    public function loadData($data)
    {
        if($this->_recursive)
        {
            $genericObject = PicoObjectParser::parseJsonRecursive($data);
            $keys = array_keys($genericObject->valueArray());
            foreach($keys as $key)
            {
                $this->{$key} = $genericObject->get($key);
            }
        }
        else
        {
            parent::loadData($data);
        }
        return $this;
    } 
}