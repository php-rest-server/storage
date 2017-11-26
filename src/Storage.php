<?php
/**
 * Engines module
 */

namespace RestCore\Storage;

use RestCore\Core\General\BaseModule;

class Storage extends BaseModule
{
    /**
     * @var Storage
     */
    protected static $instance;

    public function __construct($config = [])
    {
        parent::__construct($config);

        static::$instance = $this;
    }

    public static function getModuleConfig()
    {
        return static::$instance->getConfig();
    }
}