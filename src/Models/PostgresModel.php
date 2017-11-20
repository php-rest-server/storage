<?php
/**
 *
 */

namespace RestCore\Storage\Models;

use RestCore\Storage\Engines\PostgresStorageEngine;

abstract class PostgresModel extends StorageModel
{
    /**
     * @inheritdoc
     */
    public static function getStorageEngine()
    {
        return PostgresStorageEngine::getInstance();
    }
}