<?php
/**
 *
 */

namespace RestCore\Storages\Interfaces;

interface StorageModelInterface
{
    /**
     * @return StorageEngineInterface
     */
    public function getStorageEngine();
    /**
     * @return array
     */
    public function getFields();
}