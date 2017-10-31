<?php
/**
 *
 */

namespace RestCore\Storage\Interfaces;

interface StorageModelInterface
{
    /**
     * Get storage engine which used for this model
     *
     * @return StorageEngineInterface
     */
    public function getStorageEngine();


    /**
     * Return fields and default values for it
     *
     * @return array
     */
    public function getFields();


    /**
     * Return table / collection name for model
     * @return string
     */
    public function getTableName();
}