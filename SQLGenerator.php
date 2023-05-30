<?php

namespace kateland;
require_once 'Arrays.php';

class SQLGenerator
{
    private $table;
    private $data;
    private $conditions;

    /**
     * Construct
     * @param $table
     * @param $data
     */
    public function __construct($table, $data) {
        $this->table = $table;
        $this->data = $data;
        $this->conditions = [];
    }

    /**
     * Add data
     * @param $key
     * @param $value
     * @return void
     */
    public function addData($key, $value) {
        $this->data[$key] = $value;
    }

    /**
     * Add a condition for query
     * @param $key
     * @param $value
     * @param $operator
     * @return array
     */
    public function addCondition($key, $value, $operator = '=') {
        $this->conditions[] = [
            'column'    => $key,
            'value'     => $value,
            'operator'  => $operator
        ];
        return $this->conditions;

    }

    /**
     * Create string of conditions for query
     * @return string
     */
    public function buildConditions() {
        $conditions = [];
        foreach ($this->conditions as $condition) {
            $conditions[] = "{$condition['column']} {$condition['operator']} '{$condition['value']}'";
        }
        return implode(' AND ', $conditions);
    }

    /**
     * Create sql Insert statement
     * @return string
     */
    public function sqlInsertStatement($insertData)
    {
        $columns    = implode(', ', Arrays::getArrayKeys($insertData));
        $values     = "'" . implode("', '", $insertData) . "'";
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$values})";
        return $sql;
    }

    /**
     * Create sql Update statement
     * @param $updateFields
     * @return string
     */
    public function sqlUpdateStatement($updateFields) {
        $updates = [];
        foreach ($updateFields as $key => $value) {
            $updates[] = "{$key} = '{$value}'";
        }
        $conditions = $this->buildConditions();
        $sql = "UPDATE {$this->table} SET " . implode(', ', $updates) . " WHERE {$conditions}";

        return $sql;
    }

    /**
     * Create sql Delete statement
     * @return string
     */
    public function sqlDeleteStatement() {
        //DELETE FROM table_name WHERE condition
        $conditions = $this->buildConditions();
        $sql = "DELETE FROM {$this->table} WHERE {$conditions}";
        return $sql;
    }
}