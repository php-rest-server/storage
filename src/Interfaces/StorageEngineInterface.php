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
     * @param array $params
     * @param string $schema
     * @param int $limit
     * @return \array[]
     */
    public function find(array $params, $schema, $limit = 100);


    /**
     * @param array $params
     * @param string $schema
     * @return array|bool
     */
    public function findOne(array $params, $schema);


    /**
     * @param array $data
     * @param string $schema
     * @return bool|int
     */
    public function add(array $data, $schema);


    /**
     * @param array $params
     * @param array $data
     * @param string $schema
     * @param int $limit
     * @return bool
     */
    public function update(array $params, array $data, $schema, $limit = 100);


    /**
     * @param string $schema
     * @param array $fields
     * @return bool
     */
    public function createSchema($schema, array $fields);


    /**
     * @param string $schema
     * @param string $column
     * @param string $type
     * @return bool
     */
    public function createColumn($schema, $column, $type);
}
