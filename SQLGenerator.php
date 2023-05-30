<?php

namespace kateland;
require_once 'Arrays.php';

class SQLGenerator
{
    private $table;
    private $data;
    private $conditions;

    public function __construct($table, $data) {
        $this->table = $table;
        $this->data = $data;
        $this->conditions = [];
    }

    public function addData($key, $value) {
        $this->data[$key] = $value;
    }

    public function addCondition($key, $value, $operator = '=') {
        $this->conditions[] = [
            'column'    => $key,
            'value'     => $value,
            'operator'  => $operator
        ];
        return $this->conditions;

    }

    public function buildConditions() {
        $conditions = [];
        foreach ($this->conditions as $condition) {
            $conditions[] = "{$condition['column']} {$condition['operator']} '{$condition['value']}'";
        }
        return implode(' AND ', $conditions);
    }

    public function sqlInsertStatement()
    {
        $columns    = implode(', ', Arrays::getArrayKeys($this->data));
        $values     = "'" . implode("', '", $this->data) . "'";
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$values})";
        return $sql;
    }

    public function sqlUpdateStatement($updateFields) {
        $updates = [];
        foreach ($updateFields as $key => $value) {
            $updates[] = "{$key} = '{$value}'";
        }
        $conditions = $this->buildConditions();
        $sql = "UPDATE {$this->table} SET " . implode(', ', $updates) . " WHERE {$conditions}";

        return $sql;
    }

    public function sqlDeleteStatement() {
        //DELETE FROM table_name WHERE condition
        $conditions = $this->buildConditions();
        $sql = "DELETE FROM {$this->table} WHERE {$conditions}";
        return $sql;
    }
}