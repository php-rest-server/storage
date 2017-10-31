<?php
/**
 *
 */

namespace RestCore\Storages\Models;

use RestCore\Storages\Engines\PostgresStorageEngine;

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