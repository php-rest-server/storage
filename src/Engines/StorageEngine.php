<?php
/**
 *
 */

namespace RestCore\Storages\Engines;

use RestCore\Storages\Interfaces\StorageEngineInterface;

class StorageEngine implements StorageEngineInterface
{
    private static $instance;


    /**
     * @inheritdoc
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new static();
        }
        return self::$instance;
    }
}