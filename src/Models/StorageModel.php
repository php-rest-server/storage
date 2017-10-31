<?php
/**
 *
 */

namespace RestCore\Storage\Models;

use RestCore\Storage\Interfaces\StorageModelInterface;

abstract class StorageModel implements StorageModelInterface
{
    /**
     * A flag showing that this is a new model not yet stored in the storage
     * @var bool
     */
    public $isNew = true;

    /**
     * This array stores search parameters, in case you need to update
     * @var
     */
    protected $params = [];


    /**
     * Find model or create new
     *
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        if (!empty($params)) {
            // find model
            $data = $this->getStorageEngine()->findOne($params, $this->getTableName());
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
     */
    public function save()
    {
        $fields = array_keys($this->getFields());
        $fieldTypes = $this->getFieldTypes();
        $data = [];
        foreach ($fields as $field) {
            $val = $this->$field;
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
            $data[$field] = $val;
        }
        if ($this->isNew) {
            $newId = $this->getStorageEngine()->add($data, $this->getTableName());
            if ($newId > 0) {
                $this->{$this->getPrimaryKey()} = $newId;
            }
        } else {
            $this->getStorageEngine()->update($this->params, $data, $this->getTableName() , 1);
        }
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
                    $val = json_decode($val, true);
                    break;

                case self::FIELD_TYPE_STRING:
                    $val = (string)$val;
                    break;

                case self::FIELD_TYPE_PK:
                default:
                    break;
            }
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
}