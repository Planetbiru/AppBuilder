<?php

namespace AppBuilder;

use MagicObject\SecretObject;

class EntityInfo extends SecretObject
{
    /**
     * Active key
     *
     * @var string
     */
    public $active;
    /**
     * Draft key
     *
     * @var string
     */
    public $draft;
    
    /**
     * Admin create
     *
     * @var string
     */
    public $adminCreate;
    
    /**
     * Admin edit
     *
     * @var string
     */
    public $adminEdit;
    
    /**
     * Admin ask edit
     *
     * @var string
     */
    public $adminAskEdit;
    
    /**
     * IP create
     *
     * @var string
     */
    public $ipCreate;
    
    /**
     * IP edit
     *
     * @var string
     */
    public $ipEdit;
    
    /**
     * IP ask edit
     *
     * @var string
     */
    public $ipAskEdit;
    
    /**
     * Time create
     *
     * @var string
     */
    public $timeCreate;
    
    /**
     * Time edit
     *
     * @var string
     */
    public $timeEdit;
    
    /**
     * Time ask edit
     *
     * @var string
     */
    public $timeAskEdit;

    
    /**
     * Waiting for key
     *
     * @var string
     */
    public $waitingFor;
    
    /**
     * Variable name of database
     *
     * @var string
     */
    public $database = "database";
}