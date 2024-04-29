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
    protected $active;
    /**
     * Draft key
     *
     * @var string
     */
    protected $draft;
    
    /**
     * Admin create
     *
     * @var string
     */
    protected $adminCreate;
    
    /**
     * Admin edit
     *
     * @var string
     */
    protected $adminEdit;
    
    /**
     * Admin ask edit
     *
     * @var string
     */
    protected $adminAskEdit;
    
    /**
     * IP create
     *
     * @var string
     */
    protected $ipCreate;
    
    /**
     * IP edit
     *
     * @var string
     */
    protected $ipEdit;
    
    /**
     * IP ask edit
     *
     * @var string
     */
    protected $ipAskEdit;
    
    /**
     * Time create
     *
     * @var string
     */
    protected $timeCreate;
    
    /**
     * Time edit
     *
     * @var string
     */
    protected $timeEdit;
    
    /**
     * Time ask edit
     *
     * @var string
     */
    protected $timeAskEdit;

    
    /**
     * Waiting for key
     *
     * @var string
     */
    protected $waitingFor;
    
    /**
     * Approval ID key
     *
     * @var string
     */
    protected $approvalId;
    
    /**
     * Variable name of database
     *
     * @var string
     */
    protected $database = "database";
}