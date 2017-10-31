<?php
/**
 *
 */

namespace RestCore\Storage\Engines;

use RestCore\Storage\Interfaces\StorageEngineInterface;
use RestCore\Storage\Storage;

abstract class StorageEngine implements StorageEngineInterface
{
    private static $instance;

    abstract protected function connect($config);

    abstract protected function getStorageName();

    public function __construct()
    {
        $config = Storage::getConfig();
        $this->connect(isset($config[$this->getStorageName()]) ? $config[$this->getStorageName()] : [] );
    }

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