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
     * @param string $table
     * @return \array[]
     */
    public function find(array $params, $table);


    /**
     * @param array $params
     * @param string $table
     * @return array|bool
     */
    public function findOne(array $params, $table);


    /**
     * @param array $data
     * @param string $table
     * @return bool|int
     */
    public function add(array $data, $table);


    /**
     * @param array $params
     * @param array $data
     * @param string $table
     * @return bool
     */
    public function update(array $params, array $data, $table);
}