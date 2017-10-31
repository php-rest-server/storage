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
        $data = [];
        foreach ($fields as $field) {
            $data[$field] = $this->$field;
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

        foreach ($fields as $name => $val) {
            $this->$name = $val;
        }
        return $this;
    }
}