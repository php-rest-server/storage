<?php
/**
 * TODO: transactions
 */

namespace RestCore\Storage\Engines;

use RestCore\Core\General\Param;
use RestCore\Storage\Exceptions\StorageException;

class PostgresStorageEngine extends StorageEngine
{
    /**
     * @var \PDO
     */
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
        $statement = $this->connection->prepare(
            'SELECT * FROM "' . $table . '" WHERE ' . $this->composerWhere($params) . ' LIMIT ' . $limit . ';'
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
        if (is_array($result) && !empty($result)) {
            return $result[0];
        }
        return false;
    }


    /**
     * @inheritdoc
     * @throws \RestCore\Storage\Exceptions\StorageException
     */
    public function add(array $data, $table)
    {
        $data = array_filter($data, function ($var) {
            return null !== $var;
        });
        $columns = implode('", "', array_keys($data));
        $values = implode(', ', array_fill(0, count($data), '?'));
        $statement =
            $this->connection->prepare('INSERT INTO "' . $table . '" ("' . $columns . '") VALUES (' . $values . ');');
        if ($statement->execute(array_values($data))) {
            return $this->connection->lastInsertId();
        }

        $error = $statement->errorInfo();
        throw new StorageException($error[0] . ': ' . $error[2]);
    }


    /**
     * @inheritdoc
     * @throws \RestCore\Storage\Exceptions\StorageException
     */
    public function update(array $params, array $data, $table, $limit = 100)
    {
        $dataFields = [];
        $dataValues = [];
        foreach ($data as $field => $value) {
            $dataFields[] = '"' . $field . '" = :data_' . $field;
            $dataValues['data_' . $field] = $value;
        }
        $statement = $this->connection->prepare('UPDATE "' . $table . '" SET ' . implode(', ', $dataFields) .
            ' WHERE ' . $this->composerWhere($params) . ' /*LIMIT ' . $limit . '*/;');
        if ($statement->execute(array_merge($dataValues, $params))) {
            return true;
        }

        $error = $statement->errorInfo();
        if ($error[0] !== '00000') {
            throw new StorageException($error[0] . ': ' . $error[2]);
        }
        return false;
    }


    /**
     * @param array $params
     * @return string
     */
    protected function composerWhere(array $params)
    {
        // TODO: make smart where
        array_walk($params, function (&$item, $key) {
            $item = '"' . $key . '" = :' . $key;
        });
        return implode(' AND ', $params);
    }
}