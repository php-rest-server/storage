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
    public function getStorageEngine()
    {
        return PostgresStorageEngine::getInstance();
    }
}