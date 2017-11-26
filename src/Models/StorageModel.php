<?php
/**
 *
 */

namespace RestCore\Storage\Models;

use RestCore\Core\General\Param;
use RestCore\Storage\Exceptions\ColumnNotFoundException;
use RestCore\Storage\Exceptions\SchemaNotFoundException;
use RestCore\Storage\Interfaces\StorageModelInterface;
use RestCore\Storage\Storage;

abstract class StorageModel implements StorageModelInterface
{
    /**
     * A flag showing that this is a new model not yet stored in the storage
     * @var bool
     */
    public $isNew = true;

    /**
     * This array stores search parameters, in case you need to update
     * @var array
     */
    protected $params = [];

    /**
     * Store for old fields values
     * @var array
     */
    protected $oldFields = [];

    /**
     * Development mode enabled
     * @var bool
     */
    protected $develop;


    /**
     * Find model or create new
     *
     * @param array $params
     * @throws \RestCore\Storage\Exceptions\StorageException
     */
    public function __construct(array $params = [])
    {
        $this->develop = (new Param(Storage::getModuleConfig()))->get('develop', false);

        if ($this->develop) {
            static::developSchema();
        }

        if (!empty($params)) {
            // find model
            $data = $this->getStorageEngine()->findOne(array_keys($this->getFields()), $params, $this->getTableName());
            if (!empty($data)) {
                $this->load($data);
                $this->isNew = false;
                $this->params = $params;
                return;
            }
        }

        $this->load([]);
    }


    /**
     * Save model or update
     * @return boolean
     * @throws \RestCore\Storage\Exceptions\SchemaNotFoundException
     * @throws \RestCore\Storage\Exceptions\ColumnNotFoundException
     */
    public function save()
    {
        $fields = array_keys($this->getFields());
        $fieldTypes = $this->getFieldTypes();
        $data = [];
        foreach ($fields as $field) {
            $val = $this->$field;
            // continue if field not changed
            if (!$this->isNew && $val === $this->oldFields[$field]) {
                continue;
            }
            switch ($fieldTypes[$field]) {
                case self::FIELD_TYPE_INT:
                    $val = (int)$val;
                    break;

                case self::FIELD_TYPE_BOOL:
                    $val = (int)(bool)$val;
                    break;

                case self::FIELD_TYPE_FLOAT:
                    $val = (float)$val;
                    break;

                case self::FIELD_TYPE_ARRAY:
                    $val = json_encode($val);
                    break;

                case self::FIELD_TYPE_STRING:
                    $val = (string)$val;
                    break;

                case self::FIELD_TYPE_PK:
                default:
                    break;
            }
            $data[$field] = $val;
        }
        if (empty($data)) {
            return true;
        }
        if ($this->isNew) {
            $newId = $this->getStorageEngine()->add($data, $this->getTableName());
            if ($newId > 0) {
                $this->{$this->getPrimaryKey()} = $newId;
                return true;
            }
            return false;
        }

        return $this->getStorageEngine()->update($this->params, $data, $this->getTableName() , 1);
    }


    /**
     * Load data to model fields
     *
     * @param array $data
     * @return $this
     */
    public function load(array $data)
    {
        $fields = array_merge($this->getFields(), $data);
        $fieldTypes = $this->getFieldTypes();

        foreach ($fields as $name => $val) {
            switch ($fieldTypes[$name]) {
                case self::FIELD_TYPE_INT:
                    $val = (int)$val;
                    break;

                case self::FIELD_TYPE_BOOL:
                    $val = (bool)$val;
                    break;

                case self::FIELD_TYPE_FLOAT:
                    $val = (float)$val;
                    break;

                case self::FIELD_TYPE_ARRAY:
                    if (!is_array($val)) {
                        $val = json_decode($val, true);
                    }
                    break;

                case self::FIELD_TYPE_STRING:
                    $val = (string)$val;
                    break;

                case self::FIELD_TYPE_PK:
                default:
                    break;
            }
            $this->oldFields[$name] = $val;
            $this->$name = $val;
        }
        return $this;
    }


    /**
     * Get only safe fields
     *
     * @return array
     */
    public function getSafe()
    {
        $fields = $this->getPublicFields();
        $result = [];
        foreach ($fields as $field) {
            $result[$field] = $this->$field;
        }
        return $result;
    }


    /**
     * @inheritdoc
     */
    public static function find(array $condition)
    {
        if ((new Param(Storage::getModuleConfig()))->get('develop', false)) {
            static::developSchema();
        }

        $records = static::getStorageEngine()->find(array_keys(static::getFields()), $condition, static::getTableName());
        if (empty($records)) {
            $records = [];
        }
        /**
         * @var StorageModel[] $result
         */
        $result = [];
        foreach ($records as $id => $data) {
            $result[$id] = new static();
            $result[$id]->load($data);
            $result[$id]->isNew = false;
        }
        return $result;
    }


    /**
     * @inheritdoc
     */
    public static function getKeys()
    {
        return [];
    }


    /**
     * Check and update schema if this needed
     * @throws \RestCore\Storage\Exceptions\StorageException
     * @throws ColumnNotFoundException
     * @throws SchemaNotFoundException
     */
    protected static function developSchema()
    {
        try {
            static::getStorageEngine()->findOne(array_keys(static::getFields()), [], static::getTableName());
        } catch (ColumnNotFoundException $e) {
            if (preg_match('/column "([^"]*)" /', $e->getMessage(), $columns)) {
                static::getStorageEngine()->createColumn(
                    static::getTableName(),
                    $columns[1],
                    static::getFieldTypes()[$columns[1]]
                );
            }
        } catch (SchemaNotFoundException $e) {
            static::getStorageEngine()->createSchema(static::getTableName(), static::getFieldTypes());
        }

        $indexes = static::getStorageEngine()->getIndexes(static::getTableName());

        $notFound = array_diff(array_keys(static::getKeys()), $indexes);

        foreach ($notFound as $index) {
            static::getStorageEngine()->createIndex(
                static::getTableName(),
                $index,
                static::getKeys()[$index]['fields'],
                static::getKeys()[$index]['type']
            );
        }
    }
}
