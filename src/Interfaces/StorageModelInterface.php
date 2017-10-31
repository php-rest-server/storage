<?php
/**
 *
 */

namespace RestCore\Storage\Interfaces;

interface StorageModelInterface
{
    const FIELD_TYPE_INT = 'integer';
    const FIELD_TYPE_STRING = 'string';
    const FIELD_TYPE_PK = 'pk';
    const FIELD_TYPE_ARRAY = 'array';
    const FIELD_TYPE_BOOL = 'boolean';
    const FIELD_TYPE_FLOAT = 'float';


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
     * Return field types
     *
     * @return array
     */
    public function getFieldTypes();


    /**
     * Get list of public fields
     *
     * @return array
     */
    public function getPublicFields();


    /**
     * Return table / collection name for model
     * @return string
     */
    public function getTableName();


    /**
     * Return primary key
     * @return string
     */
    public function getPrimaryKey();
}
