<?php
namespace kateland;

require_once 'FileHandler.php';
require_once 'CSVHandler.php';
require_once 'SQLGenerator.php';

use kateland\{FileHandler, CSVHandler, SQLGenerator};

class CountriesSQL {
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
        $data = $csv->getDataWithKeys($filename);
        foreach ($data as $entry) {
            $sql[] = $this->getValidSqlCountries($entry, $table);
        }
        $newFile = 'countries.sql';
        if (!file_exists($newFile)) {
            fopen('countries.sql', 'w+');
        }
        file_put_contents($newFile, implode("; ", $sql));
    }

    public function getValidSqlCountries($entry, $table) {
        $sqlGen = new SQLGenerator($table, $entry);
        $titleChangedDE = $entry['title_de_changed'];
        $isDeleted = $entry['is_deleted'];
        $isNew = $entry['is_new_entry'];

        $iso2       = $entry['iso2'];
        $iso3       = $entry['iso3'];
        $titleDE    = $entry['title_de'];
        $titleEN    = $entry['title_en'];
        $titleFR    = $entry['title_fr'];
        $titleIT    = $entry['title_it'];
        $postData   = $entry['title_post_sort'];

        $sqlGen->addCondition('iso2', $iso2);

        if ($isDeleted) {
            $sqlGen->addCondition('title_de', $titleDE);
            $sql = $sqlGen->sqlDeleteStatement();
        } elseif ($titleChangedDE && !$isNew) {
            $updateFields = [
                'iso3'              => $iso3,
                'title_de'          => $titleDE,
                'title_post_sort'   => $postData
            ];
            $sql = $sqlGen->sqlUpdateStatement($updateFields);
            //update title_de, post, iso3
        } elseif ($isNew) {
            $insertData = [
                'iso2'              => $iso2,
                'iso3'              => $iso3,
                'title_de'          => $titleDE,
                'title_en'          => $titleEN,
                'title_fr'          => $titleFR,
                'title_it'          => $titleIT,
                'title_post_sort'   => $postData
            ];
            $sql = $sqlGen->sqlDeleteStatement();
            $sql .= "; " . $sqlGen->sqlInsertStatement($insertData);
        } else {
            $updateFields = [
                'iso3'              => $iso3,
                'title_post_sort'   => $postData
            ];
            $sql = $sqlGen->sqlUpdateStatement($updateFields);
            //update iso3, post
        }
        return $sql;
    }

}

$filename = 'countries.csv';
$table = 'orm_country';

$test = new CountriesSQL();
$test->run($filename, $table);