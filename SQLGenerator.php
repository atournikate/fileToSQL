<?php
/**
 * Static class to create sql statements
 */
namespace file2sql;

use Exception;

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
        return " `$key` $operator" .  '"' . $value . '"';
    }

    /**
     * Create sql Insert statement
     * @param $table
     * @param $data
     * @return string
     */
    public static function sqlInsertStatement($table, $data): string
    {
        //$columns    = implode(', ', array_keys($data));
        $values = [];
        foreach ($data as $key => $value) {
            $values[] = " `$key` = " . '"' . $value . '"';
        }
        $values     = "'" . implode("', '", $data) . "'";
        $sql       = "INSERT INTO $table SET $values;";
        return $sql. "\n";
    }

    /**
     * @throws Exception
     */
    public static function sqlMultiIndividualInsertStatements($table, $data) {
        if (empty($data)) {
            throw new Exception("No data");
        }

        $pattern = "/(?<=[a-zA-Z])'(?=[a-zA-Z]|[^\u{0001}-\u{007F}]|[À-ÿ])/";
        $replace = "\\'";

        $sql     = "";

        foreach ($data as $entry) {
            $entryValues = [];
            foreach ($entry as $key => $value) {
                $key = self::sanitizeValue($key, $pattern, $replace);
                $value = self::sanitizeValue($value, $pattern, $replace);

                $entryValues[] = '`' . $key . '` = "' . $value . '"';
            }

            $sql .= "INSERT INTO $table SET " . implode(", ", $entryValues);
            $sql .= ";" . PHP_EOL;
        }

        return  $sql;
    }

    private static function sanitizeValue($value, $pattern, $replace) {
        if (is_array($value)) {
            $sanitizedValues = [];
            foreach ($value as $item) {
                $sanitizedValues[] = self::sanitizeValue($item, $pattern, $replace);
            }
            return $sanitizedValues;
        }

        $sanitizedValue = str_replace("\0", "", $value);
        return preg_replace($pattern, $replace, $sanitizedValue);
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


        $sql = "UPDATE $table SET " . implode(', ', $updates) . " WHERE $condition;";

        return $sql;
    }

    /**
     * Create sql Delete statement
     * @param $table
     * @param $conditions
     * @return string
     */
    public static function sqlUpdateDeletedStatement($table, $conditions): string
    {
        //DELETE FROM table_name WHERE condition
        $sql= "UPDATE $table SET `is_deleted` = 1 WHERE $conditions;";
        return  $sql. "\n";
    }

    public static function sqlDeleteStatement($table, $conditions) {
        $sql = "DELETE FROM $table WHERE $conditions;";
        return $sql . "\n";
    }

    /**
     * Create sql Drop Table statement
     * @param $table
     * @return string
     */
    public static function sqlDropTable($table): string
    {
        $sql= "DROP TABLE IF EXISTS $table;";
        return $sql. "\n";
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