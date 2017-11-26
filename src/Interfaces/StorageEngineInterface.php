<?php
/**
 *
 */

namespace RestCore\Storage\Interfaces;


interface StorageEngineInterface
{
    /**
     * @return StorageEngineInterface
     */
    public static function getInstance();


    /**
     * @param array $fields
     * @param array $params
     * @param string $schema
     * @param int $limit
     * @return array[]
     * @throws \RestCore\Storage\Exceptions\StorageException
     */
    public function find(array $fields, array $params, $schema, $limit = 100);


    /**
     * @param array $fields
     * @param array $params
     * @param string $schema
     * @return array|bool
     * @throws \RestCore\Storage\Exceptions\StorageException
     */
    public function findOne(array $fields, array $params, $schema);


    /**
     * @param array $data
     * @param string $schema
     * @return bool|int
     * @throws \RestCore\Storage\Exceptions\StorageException
     */
    public function add(array $data, $schema);


    /**
     * @param array $params
     * @param array $data
     * @param string $schema
     * @param int $limit
     * @return bool
     * @throws \RestCore\Storage\Exceptions\StorageException
     */
    public function update(array $params, array $data, $schema, $limit = 100);


    /**
     * @param string $schema
     * @param array $fields
     * @return bool
     * @throws \RestCore\Storage\Exceptions\StorageException
     */
    public function createSchema($schema, array $fields);


    /**
     * @param string $schema
     * @param string $column
     * @param string $type
     * @return bool
     * @throws \RestCore\Storage\Exceptions\StorageException
     */
    public function createColumn($schema, $column, $type);


    /**
     * @param $schema
     * @param $name
     * @param $columns
     * @param $type
     * @return bool
     * @throws \RestCore\Storage\Exceptions\StorageException
     */
    public function createIndex($schema, $name, $columns, $type);


    /**
     * Returns indexes for schema
     *
     * @param $schema
     * @return array ['index_name' => ['field1',...]
     */
    public function getIndexes($schema);


    /**
     * Start transaction
     */
    public function transactionBegin();


    /**
     * Commit transaction
     */
    public function transactionCommit();


    /**
     * Rollback transaction
     */
    public function transactionRollback();
}
