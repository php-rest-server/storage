<?php
/**
 *
 */

namespace RestCore\Storage\Engines;

use RestCore\Core\General\Param;

class PostgresStorageEngine extends StorageEngine
{
    private $connection;

    /**
     * @inheritdoc
     */
    protected function getStorageName()
    {
        return 'postgres';
    }

    /**
     * @inheritdoc
     */
    protected function connect($config)
    {
        $config = new Param($config);
        $this->connection = new \PDO(
            $config->get('dsn', 'pgsql:localhost'),
            $config->get('username', 'postgres'),
            $config->get('password', '')
        );
    }

    /**
     * @inheritdoc
     */
    public function find(array $params, $table)
    {
        //$this
    }

    /**
     * @inheritdoc
     */
    public function findOne(array $params, $table)
    {
        // TODO: Implement findOne() method.
    }

    /**
     * @inheritdoc
     */
    public function add(array $data, $table)
    {
        // TODO: Implement add() method.
    }

    /**
     * @inheritdoc
     */
    public function update(array $params, array $data, $table)
    {
        // TODO: Implement update() method.
    }

}