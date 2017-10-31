<?php
/**
 *
 */

namespace RestCore\Storages\Interfaces;


interface StorageEngineInterface
{
    /**
     * @return StorageEngineInterface
     */
    public static function getInstance();
}