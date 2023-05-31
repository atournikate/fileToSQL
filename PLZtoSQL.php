<?php
namespace kateland;

require_once 'FileHandler.php';
require_once 'CSVHandler.php';
require_once 'SQLGenerator.php';

use kateland\{FileHandler, CSVHandler, SQLGenerator};

class PLZtoSQL {
    public function run($filename, $table) {
        $this->createSqlFile($filename, $table);
    }

    /**
     * Create SQL File
     * @param $filename
     * @param $table
     * @return void
     */
    public function createSqlFile($filename, $table)
    {
        FileHandler::setFilename($filename);
        $csv = new CSVHandler();
        $mkTable = $this->createTable($table);
        $data = $csv->getDataWithoutLastKey($filename);
        foreach ($data as $entry) {
            $sql[] = $this->getValidSqlPlz($table, $entry);
        }
        $newFile = 'plz.sql';
        if (!file_exists($newFile)) {
            fopen('plz.sql', 'w+');
        }
        $create = $mkTable . "\n" . implode("\n ", $sql);

        file_put_contents($newFile, $create);
    }

    //iso2,plz,name,state_name,state_code,county_name,county_code,district_name,district_code
    
    public function getValidSqlPlz($table, $entry) {
        $sqlGen = new SQLGenerator($table, $entry);
        
        $insertData = [
            'iso2'              => $entry['iso2'],
            'plz'               => $entry['plz'],
            'name'              => $entry['name'],
            'state_code'        => $entry['state_code'],
            'county_code'       => $entry['county_code'],
            'district_code'     => $entry['district_code']
        ];
        //$sql = $sqlGen->sqlDeleteStatement();
        $sql = $sqlGen->sqlInsertStatement($insertData);
        
        return $sql;
    }

    public function createTable($table) {
        $columns = [
            ['id', 'INT', 'AUTO_INCREMENT PRIMARY KEY'],
            ['iso2', 'VARCHAR(255)', ''],
            ['plz', 'VARCHAR(255)', ''],
            ['name', 'VARCHAR(255)', ''],
            ['state_code', 'VARCHAR(255)', ''],
            ['county_code', 'VARCHAR(255)', ''],
            ['district_code', 'VARCHAR(255)', ''],
        ];
        $sqlGen = new SQLGenerator($table);
        foreach ($columns as $column) {
            $sqlGen->addTableColumn($column[0], $column[1], $column[2]);
        }

        $sql = $sqlGen->sqlDropTable();
        $sql .= "\n" . $sqlGen->sqlCreateTable();
        return $sql;
    }

}

$filename = 'de_plz.csv';
$table = 'plz';

$test = new PLZtoSQL();
$test->run($filename, $table);