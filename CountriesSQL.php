<?php
namespace file2sql;

//require_once 'FileHandler.php';
use Exception;

require_once 'CSVHandler.php';
require_once 'SQLGenerator.php';

class CountriesSQL {

    /**
     * Create SQL File
     * @param $filename
     * @param $table
     * @return void
     * @throws Exception
     */
    public function createSqlFile($filename, $table): void
    {
        $csv    = new CSVHandler();
        $data   = $csv->getFormattedData($filename);
        $sql    = [];
        foreach ($data as $entry) {
            $sql[] = $this->getValidSqlCountries($entry, $table);
        }
        $newFile = 'countries.sql';
        if (!file_exists($newFile)) {
            fopen('countries.sql', 'w+');
        }
        file_put_contents($newFile, implode("\n ", $sql));
    }

    /**
     * @param $entry
     * @param $table
     * @return string
     */
    public function getValidSqlCountries($entry, $table): string
    {
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

        $condition = SQLGenerator::buildCondition('iso2', $iso2);

        if ($isDeleted) {
            $condition .= SQLGenerator::buildCondition('title_de', $titleDE);
            $sql = SQLGenerator::sqlDeleteStatement($table, $condition) . PHP_EOL;
        } elseif ($titleChangedDE && !$isNew) {
            $updateFields = [
                'iso3'              => $iso3,
                'title_de'          => $titleDE,
                'title_post_sort'   => $postData
            ];
            $sql = SQLGenerator::sqlUpdateStatement($table, $updateFields, $condition) . PHP_EOL;
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
            $sql = SQLGenerator::sqlDeleteStatement($table, $condition);
            $sql .= SQLGenerator::sqlInsertStatement($table, $insertData) . PHP_EOL;
        } else {
            $updateFields = [
                'iso3'              => $iso3,
                'title_post_sort'   => $postData
            ];
            $sql = SQLGenerator::sqlUpdateStatement($table, $updateFields, $condition) . PHP_EOL;
            //update iso3, post
        }
        return $sql;
    }

}

$filename = 'countries.csv';
$table = 'orm_country';

$test = new CountriesSQL();
try {
    $test->createSqlFile($filename, $table);
} catch (Exception $e) {
    echo "An Exception occurred: " . $e->getMessage();
}