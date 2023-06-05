<?php
/**
 * Static class to create sql statements
 */
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

    /**
     * Create sql Insert statements for multiple inserts
     * @param $table
     * @param $data
     * @return string
     */
    public static function sqlMultiLineInsertStatement($table, $data): string
    {
        if (empty($data)) {
            return '';
        }

        $columns = array_keys($data[0]);
        $pattern = "/(?<=[a-zA-Z])'(?=[a-zA-Z]|[^\u{0001}-\u{007F}]|[À-ÿ])/";
        $replace = "\\'";

        $values  = [];

        foreach ($data as $entry) {
            $entry = array_map(function ($value) use ($pattern, $replace) {
                $sanitizedValue = str_replace("\0", "", $value);
                return preg_replace($pattern, $replace, $sanitizedValue);
            }, $entry);

            $entry = array_filter($entry);
            $values[] = '"' . implode('", "', $entry) . '"';
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
            $updates[] = " `$key` =" . '"' . $value . '"';
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

    /**
     * Create sql Drop Table statement
     * @param $table
     * @return string
     */
    public static function sqlDropTable($table): string
    {
        return "DROP TABLE IF EXISTS $table;";
    }

    /**
     * Add a columns to new table
     * @param $columnName
     * @param $dataType
     * @param string $options
     * @return string
     */
    public static function addTableColumn($columnName, $dataType, string $options = ''): string
    {
        return "$columnName $dataType $options";
    }

    /**
     * Create sql Table
     * @param $table
     * @param $data
     * @return string
     */
    public static function sqlCreateTable($table, $data): string
    {
        $columns = implode("\n", $data);
        return "CREATE TABLE $table (\n$columns\n);";
    }
}