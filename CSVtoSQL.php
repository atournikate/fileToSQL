<?php

namespace file2sql;

use Exception;

require_once 'CSVHandler.php';
require_once 'SQLGenerator.php';

class CSVtoSQL
{
    /**
     * Cycle through data in CSV file, create sql file of insert statements for each one
     * @param $filepath
     * @param $table
     * @return void
     * @throws Exception
     */
    public function processCSVtoSQLFile($filepath, $table): void
    {
        $data = $this->convertCSVToArray($filepath);
        $num = count($data);
        for ($i = 0; $i < $num; $i++) {
            //$data[$i] accesses data for each individual country zipcode file in a nested array
            $insert = $this->createSQLInsertStatements($data[$i], $table);

            $this->printResultToFile($insert, $data[$i][0]['iso2'] . ".sql");
        }
    }

    /**
     * Scan directory and return files
     * @param $filepath
     * @return array|false
     * @throws Exception
     */
    private function processDirectory($filepath)
    {
        if (is_dir($filepath)) {
            $files = scandir($filepath);
        } else {
            throw new Exception("Invalid directory path");
        }
        return $files;
    }

    /**
     * Convert CSV data to Array
     * @param $filepath
     * @return array
     * @throws Exception
     */
    private function convertCSVToArray($filepath): array
    {
        $csv = new CSVHandler();
        $files = $this->processDirectory($filepath);
        $data = [];
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $data[] = $csv->getFormattedData($filepath . "/" . $file);
            }
        }

        return $data;
    }

    /**
     * Create SQL delete and insert statements
     * @param $data
     * @param $table
     * @return string
     * @throws Exception
     */
    private function createSQLInsertStatements($data, $table): string
    {
        $iso2        = $data[0]['iso2'];

        $condition  = SQLGenerator::buildCondition('iso2', $iso2);
        $delete     = SQLGenerator::sqlDeleteStatement($table, $condition);

        $insertStatements = $this->getMultiIndividualInserts($table, $data);

        return $delete . "\n" . $insertStatements;
    }

    /**
     * Create multiline SQL insert statement
     * @param $table
     * @param $data
     * @return string
     * @throws Exception
     */
/*    private function getMultiLineInserts($table, $data): string
    {
        return SQLGenerator::sqlMultiLineInsertStatement($table, $data);
    }*/

    private function getMultiIndividualInserts($table, $data): string {
        return SQLGenerator::sqlMultiIndividualInsertStatements($table, $data);
    }

    /**
     * Create SQL table with Drop and Create Statement
     * @param $table
     * @param $columns
     * @return string
     */
    /*private function createTable($table, $columns): string
    {
        $cols = [];
        foreach ($columns as $column) {
            $cols[] = SQLGenerator::addTableColumn($column[0], $column[1], $column[2]);
        }
        $sql = SQLGenerator::sqlDropTable($table);
        $sql .= "\n" . SQLGenerator::sqlCreateTable($table, $cols);

        return $sql;
    }*/

    /**
     * Write SQL to file
     * @param $data
     * @param string $fileName
     * @return void
     */
    private function printResultToFile($data, string $fileName = 'test.sql'): void
    {

        $filepath = 'sql/' . $fileName;
        if(!is_dir('sql')) {
            mkdir('sql');
        }

        if (file_exists($filepath)) {
            unlink($filepath);
        }

        if (is_array($data)) {
            $content = implode("\n", $data);
        } else {
            $content = $data;
        }

        file_put_contents($filepath, $content);
    }

    /**
     * Log errors
     * @param $message
     * @param string $logFile
     * @return void
     */
    public function logError($message, string $logFile = 'error.log'): void
    {
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
try {
    $test->processCSVtoSQLFile($filepath, $table);
} catch (Exception $e) {
    $test->logError($e->getMessage());
}
