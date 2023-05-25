<?php

namespace kateland;

class SQLGenerator
{
    private static $table;

    public function __construct(string $table ) {
        $this->table = $table;
    }

    public static function sqlInsertStatement(array $keys, array $values) {
        //INSERT INTO table_name (key1, key2, key3...) VALUES (val1, val2, val3);
        $lastKey = end($keys);
        $lastValue = end($values);
        $pre = "INSERT INTO " . self::$table . " (";
        $sql = $pre;
        foreach ($keys as $key) {
            $sql .= "$key";
            if (!$lastKey == $key) {
                $sql .= ", ";
            }
        }

        $sql .= ") VALUES (";

        foreach ($values as $value) {
            $sql .= "'$value'";
            if (!$lastValue == $value) {
                $sql .= ", ";
            }
        }
        $sql .= "); ";

        print_r($sql);
        return $sql;
    }

    public static function sqlUpdateStatement() {
        //UPDATE table_name SET key=val, key1=val1, ... WHERE condition
    }

    public static function sqlDeleteStatement() {
        //DELETE FROM table_name WHERE condition
    }
}