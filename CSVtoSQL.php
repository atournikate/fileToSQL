<?php

namespace file2sql;

require_once 'FileHandler.php';
require_once 'CSVHandler.php';
require_once 'SQLGenerator.php';

use file2sql\{CSVHandler, FileHandler, SQLGenerator};

class CSVtoSQL
{
    public function run($filepath, $table, $columns = null) {
        $this->processFiles($filepath, $table);
    }

    public function createSQLPLZ($filepath, $table, $columns) {
        $this->createTable($table, $columns);
        $this->createInserts($filepath, $table, $columns);
    }


    private function processDirectory($filepath)
    {
        if (is_dir($filepath)) {
            $files = scandir($filepath);
        } else {
            throw new \Exception("Invalid directory path");
        }
        return $files;
    }

    private function getFileData($filepath) {
        $csv = new CSVHandler();
        $files = $this->processDirectory($filepath);
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $data[] = $csv->getFormattedData($filepath . "/" . $file);
            }
        }

        return $data;
    }

    private function createInserts($data, $table) {
        $iso        = $data[0]['iso2'];

        $condition  = SQLGenerator::buildCondition('iso2', $iso);
        $delete     = SQLGenerator::sqlDeleteStatement($table, $condition);

        $insertStatements = $this->getInsertByLand($table, $data);

        return $delete . "\n" . $insertStatements;
    }

    public function getInsertByLand($table, $data) {
        return SQLGenerator::sqlMultiLineInsertStatement($table, $data);
    }

    public function createTable($table, $columns) {
        $cols = [];
        foreach ($columns as $column) {
            $cols[] = SQLGenerator::addTableColumn($column[0], $column[1], $column[2]);
        }
        $sql = SQLGenerator::sqlDropTable($table);
        $sql .= "\n" . SQLGenerator::sqlCreateTable($table, $cols);

        return $sql;
    }

    public function printResultToFile($data, $fileName = 'test.sql') {
        $filepath = 'sql/' . $fileName;
        if (!file_exists($filepath)) {
            mkdir('sql');
            fopen($filepath, 'w+');
        }

        if (is_array($data)) {
            file_put_contents($fileName, implode("\n", $data));
        } else {
            file_put_contents($filepath, $data);
        }
    }

    private function processFiles($filepath, $table) {
        $data = $this->getFileData($filepath);
        $num = count($data);
        for ($i = 0; $i < $num; $i++) {
            //$data[$i] accesses data for each individual country zipcode file in a nested array
            $insert = $this->createInserts($data[$i], $table);
            $this->printResultToFile($insert, $data[$i][0]['iso2'] . ".sql");
        }
    }

    public function logError($message, $logFile = 'error.log') {
        if (!file_exists($logFile)) {
            fopen($logFile, 'a+');
        }
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] $message\n";
        error_log($logMessage, 3, $logFile);
    }

}

$filepath = "csv";
$table = "orm_zip_code";
/*$columns = [
    ['id', 'INT', 'AUTO_INCREMENT PRIMARY KEY'],
    ['iso2', 'VARCHAR(255)', ''],
    ['plz', 'VARCHAR(255)', ''],
    ['location', 'VARCHAR(255)', ''],
];*/
$test = new CSVtoSQL();
$test->run($filepath, $table);
