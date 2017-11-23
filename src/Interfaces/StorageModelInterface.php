<?php
/**
 *
 */

namespace RestCore\Storage\Interfaces;

interface StorageModelInterface
{
    // field types
    const FIELD_TYPE_INT = 'integer';
    const FIELD_TYPE_STRING = 'string';
    const FIELD_TYPE_PK = 'pk';
    const FIELD_TYPE_ARRAY = 'array';
    const FIELD_TYPE_BOOL = 'boolean';
    const FIELD_TYPE_FLOAT = 'float';

    // index types
    const INDEX_TYPE_UNIQUE = 'unique';
    const INDEX_TYPE_INDEX = 'index';


    /**
     * Get storage engine which used for this model
     *
     * @return StorageEngineInterface
     */
    public static function getStorageEngine();


    /**
     * Return fields and default values for it
     *
     * @return array
     */
    public static function getFields();


    /**
     * Return field types
     *
     * @return array
     */
    public static function getFieldTypes();


    /**
     * Get list of public fields
     *
     * @return array
     */
    public static function getPublicFields();


    /**
     * Return table / collection name for model
     * @return string
     */
    public static function getTableName();


    /**
     * Return primary key
     * @return string
     */
    public static function getPrimaryKey();


    /**
     * Return other keys of model
     *
     * [
     *  'keyName' => ['type' => self::INDEX_TYPE_INDEX, 'fields' => [...]],
     *  ...
     * ]
     *
     * Uses only for debug mode!
     *
     * @return array
     */
    public static function getKeys();


    /**
     * Return list of models which satisfying the condition
     * @param array $condition
     * @return static[]
     * @throws \RestCore\Storage\Exceptions\StorageException
     */
    public static function find(array $condition);
}
