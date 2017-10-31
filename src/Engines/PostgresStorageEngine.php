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
            $config->get('dsn', 'pgsql:host=localhost'),
            $config->get('username', 'postgres'),
            $config->get('password', '')
        );
    }


    /**
     * @inheritdoc
     */
    public function find(array $params, $table, $limit = 100)
    {
        $keys = array_keys($params);
        // TODO: доделать поиск
        $statement = $this->connection->prepare(
            'SELECT * FROM ' . $table . ' WHERE ' . $this->composerWhere($params) . ' LIMIT ' . $limit
        );
        $statement->execute($params);
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }


    /**
     * @inheritdoc
     */
    public function findOne(array $params, $table)
    {
        $result = $this->find($params, $table, 1);
        if (is_array($result)) {
            return $result[0];
        }
        return false;
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
    public function update(array $params, array $data, $table, $limit = 100)
    {
        // TODO: Implement update() method.
    }


    /**
     * @param array $params
     * @return string
     */
    protected function composerWhere(array $params)
    {
        array_walk($params, function ($item, $key) {
            return $key . '= :' . $item;
        });
        return implode(' AND ', $params);
    }
}