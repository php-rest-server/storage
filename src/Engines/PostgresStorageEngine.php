<?php
/**
 * TODO: transactions
 */

namespace RestCore\Storage\Engines;

use RestCore\Core\General\Param;
use RestCore\Storage\Exceptions\ColumnNotFoundException;
use RestCore\Storage\Exceptions\SchemaNotFoundException;
use RestCore\Storage\Exceptions\StorageException;
use RestCore\Storage\Interfaces\StorageModelInterface;

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
    public function find(array $fields, array $params, $schema, $limit = 100)
    {
        if (empty($fields)) {
            $fieldsSelect = '*';
        } else {
            $fieldsSelect = '"' . implode('", "', $fields) . '"';
        }

        $statement = $this->connection->prepare(
            'SELECT ' . $fieldsSelect . ' FROM "' . $schema . '" ' .
            (!empty($params) ? 'WHERE ' . $this->composeWhere($params) . ' ' : '') .
            'LIMIT ' . $limit . ';'
        );
        if ($statement->execute($params)) {
            return $statement->fetchAll(\PDO::FETCH_ASSOC);
        }

        $error = $statement->errorInfo();
        if ($error[0] === '42P01') {
            throw new SchemaNotFoundException($error[0] . ': ' . $error[2]);
        }
        throw new StorageException($error[0] . ': ' . $error[2]);
    }


    /**
     * @inheritdoc
     */
    public function findOne(array $fields, array $params, $schema)
    {
        $result = $this->find($fields, $params, $schema, 1);
        if (is_array($result) && !empty($result)) {
            return $result[0];
        }
        return false;
    }


    /**
     * @inheritdoc
     */
    public function add(array $data, $schema)
    {
        $data = array_filter($data, function ($var) {
            return null !== $var;
        });
        $columns = implode('", "', array_keys($data));
        $values = implode(', ', array_fill(0, count($data), '?'));
        $statement =
            $this->connection->prepare('INSERT INTO "' . $schema . '" ("' . $columns . '") VALUES (' . $values . ');');
        if ($statement->execute(array_values($data))) {
            return $this->connection->lastInsertId();
        }

        $error = $statement->errorInfo();

        if ($error[0] === '42P01') {
            throw new SchemaNotFoundException($error[0] . ': ' . $error[2]);
        }

        throw new StorageException($error[0] . ': ' . $error[2]);
    }


    /**
     * @inheritdoc
     */
    public function createSchema($schema, array $fields)
    {
        $fieldRecord = [];

        foreach ($fields as $field => $type) {
            $record = '"' . $field . '" ';
            switch ($type) {
                case StorageModelInterface::FIELD_TYPE_PK:
                    $record .= 'serial PRIMARY KEY';
                    break;

                case StorageModelInterface::FIELD_TYPE_INT:
                    $record .= 'integer';
                    break;

                case StorageModelInterface::FIELD_TYPE_ARRAY:
                    $record .= 'json';
                    break;

                case StorageModelInterface::FIELD_TYPE_FLOAT:
                    $record .= 'double precision';
                    break;

                case StorageModelInterface::FIELD_TYPE_BOOL:
                    $record .= 'boolean';
                    break;

                case StorageModelInterface::FIELD_TYPE_STRING:
                default:
                    $record .= 'text';
                    break;
            }
            $fieldRecord[] = $record;
        }

        $fieldRecord = implode(',', $fieldRecord);

        $statement = $this->connection->query('CREATE TABLE "' . $schema . '" (' . $fieldRecord . ');');

        if ($statement) {
            return true;
        }

        $error = $this->connection->errorInfo();
        throw new StorageException($error[0] . ': ' . $error[2]);
    }


    /**
     * @inheritdoc
     */
    public function update(array $params, array $data, $schema, $limit = 100)
    {
        $dataFields = [];
        $dataValues = [];
        foreach ($data as $field => $value) {
            $dataFields[] = '"' . $field . '" = :data_' . $field;
            $dataValues['data_' . $field] = $value;
        }
        $statement = $this->connection->prepare('UPDATE "' . $schema . '" SET ' . implode(', ', $dataFields) .
            ' WHERE ' . $this->composeWhere($params) . ' /*LIMIT ' . $limit . '*/;');
        if ($statement->execute(array_merge($dataValues, $params))) {
            return true;
        }

        $error = $statement->errorInfo();
        if ($error[0] !== '00000') {
            if ($error[0] === '42703') {
                throw new ColumnNotFoundException($error[0] . ': ' . $error[2]);
            }
            throw new StorageException($error[0] . ': ' . $error[2]);
        }
        return false;
    }


    /**
     * @param array $params
     * @return string
     */
    protected function composeWhere(array &$params)
    {
        // TODO: make smart where
        $raw = $params;
        array_walk($raw, function (&$item, $key) use (&$params) {
            if (is_array($item)) {
                switch ($item[1]) {
                    case 'NOT':
                        $params[$key] = $item[2];
                        $item = '"' . $item[0] . '" != :' . $key;
                        break;
                }
            } else {
                $item = '"' . $key . '" = :' . $key;
            }
        });

        return implode(' AND ', $raw);
    }

    /**
     * @inheritdoc
     */
    public function createColumn($schema, $column, $type)
    {
        switch ($type) {
            case StorageModelInterface::FIELD_TYPE_PK:
                $record = 'serial PRIMARY KEY';
                break;

            case StorageModelInterface::FIELD_TYPE_INT:
                $record = 'integer';
                break;

            case StorageModelInterface::FIELD_TYPE_ARRAY:
                $record = 'json';
                break;

            case StorageModelInterface::FIELD_TYPE_FLOAT:
                $record = 'double precision';
                break;

            case StorageModelInterface::FIELD_TYPE_BOOL:
                $record = 'boolean';
                break;

            case StorageModelInterface::FIELD_TYPE_STRING:
            default:
                $record = 'text';
                break;
        }

        $statement =
            $this->connection->query('ALTER TABLE "' . $schema . '" ADD COLUMN "' . $column . '" ' . $record . ';');

        if ($statement) {
            return true;
        }

        $error = $this->connection->errorInfo();
        throw new StorageException($error[0] . ': ' . $error[2]);
    }

    /**
     * @inheritdoc
     */
    public function createIndex($schema, $name, $columns, $type)
    {
        if (empty($name)) {
            $name = $schema . '_' . $type . '_' . implode('_', $columns);
        }
        $statement =
            $this->connection->query('CREATE ' . ($type === StorageModelInterface::INDEX_TYPE_UNIQUE ? 'UNIQUE ' : '') .
                'INDEX "' . $name . '" ON "' . $schema . '" ("' . implode('", "', $columns) . '");');

        if ($statement) {
            return true;
        }

        $error = $this->connection->errorInfo();
        throw new StorageException($error[0] . ': ' . $error[2]);
    }

    /**
     * @inheritdoc
     */
    public function getIndexes($schema)
    {
        try {
            $data = $this->find(
                ['indexname'],
                ['tablename' => $schema,],
                'pg_indexes'
            );
            return array_column($data, 'indexname');
        } catch (\Exception $e) {
            return [];
        }
    }
}
