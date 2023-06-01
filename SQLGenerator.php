<?php

namespace file2sql;

class SQLGenerator
{

    /**
     * Create string of conditions for query
     * @param $key
     * @param $value
     * @param string $operator
     * @return string
     */
    public static function buildCondition($key, $value, string $operator = '='): string
    {
        return "$key $operator '$value'";
    }

    /**
     * Create sql Insert statement
     * @param $table
     * @param $data
     * @return string
     */
    public static function sqlInsertStatement($table, $data): string
    {
        $columns    = implode(', ', array_keys($data));
        $values     = "'" . implode("', '", $data) . "'";
        return "INSERT INTO $table ($columns) VALUES ($values);";
    }

    public static function sqlMultiLineInsertStatement($table, $data): string
    {
        if (empty($data)) {
            return '';
        }

        $columns = array_keys($data[0]);
        $values = [];

        foreach ($data as $entry) {
            $values[] = "'" . implode(", ", $entry) . "'";
        }

        $colString = implode(", ", $columns);
        $valString = "(" . implode("), \n(", $values) . ")";

        return  "INSERT INTO $table ($colString)\nVALUES\n$valString;";
    }

    /**
     * Create sql Update statement
     * @param $table
     * @param $data
     * @param $condition
     * @return string
     */
    public static function sqlUpdateStatement($table, $data, $condition): string
    {
        $updates = [];
        foreach ($data as $key => $value) {
            $updates[] = "$key = '$value'";
        }

        return "UPDATE $table SET " . implode(', ', $updates) . " WHERE $condition;";
    }

    /**
     * Create sql Delete statement
     * @param $table
     * @param $conditions
     * @return string
     */
    public static function sqlDeleteStatement($table, $conditions): string
    {
        //DELETE FROM table_name WHERE condition
        return "DELETE FROM $table WHERE $conditions;";
    }

    public static function sqlDropTable($table): string
    {
        return "DROP TABLE IF EXISTS $table;";
    }

    public static function addTableColumn($columnName, $dataType, $options = ''): string
    {
        $columns = "$columnName $dataType $options";
        return $columns;
    }

    public static function sqlCreateTable($table, $data): string
    {
        $columns = implode(",\n", $data);
        return "CREATE TABLE $table (\n$columns\n);";
    }
}