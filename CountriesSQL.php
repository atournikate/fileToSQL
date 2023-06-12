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
     * Generates a Delete Statement for new entries
     * @param $table
     * @param $condition
     * @param $refConditions
     * @return string
     */
    private function generateDeletedStatements($table, $condition, $refConditions): string
    {
        $sql = SQLGenerator::sqlUpdateDeletedStatement($table, $condition) . PHP_EOL;
        $sql .= SQLGenerator::sqlUpdateDeletedStatement('sys_reference_data', $refConditions) . PHP_EOL;
        return $sql;
    }

    /**
     * Generates a Statements that sets an existing value to deleted
     * @param $table
     * @param $updateFields
     * @param $condition
     * @param $refConditions
     * @return string
     */
    private function generateUpdateDeletedStatements($table, $updateFields, $condition): string
    {
        $sql = SQLGenerator::sqlUpdateStatement($table, $updateFields, $condition) . PHP_EOL;
        return $sql;
    }

    /**
     * @throws Exception
     */
    private function generateNewEntryStatements($table, $insertData, $condition, $refConditions): string
    {
        $sql = SQLGenerator::sqlDeleteStatement($table, $condition);
        $sql .= SQLGenerator::sqlMultiIndividualInsertStatements($table, $insertData) . PHP_EOL;
        $sql .= SQLGenerator::sqlDeleteStatement('sys_reference_data', $refConditions) . PHP_EOL;
        return $sql;
    }

    private function generateUpdateStatements($table, $updateFields, $condition): string
    {
        return SQLGenerator::sqlUpdateStatement($table, $updateFields, $condition) . PHP_EOL;
    }

    private function buildReferenceConditions($iso2): string
    {
        $refCondition = [
            'reference_type'    => 20,
            'reference_code'    => $iso2
        ];

        $refConditions = "";
        $index = 0;
        $totalConditions = count($refCondition);

        foreach ($refCondition as $key => $value) {
            $refConditions .= SQLGenerator::buildCondition($key, $value);
            if ($index < $totalConditions - 1) {
                $refConditions .= " AND ";
            }
            $index++;
        }

        return $refConditions;
    }

    /**
     * @param $entry
     * @param $table
     * @return string
     * @throws Exception
     */
    public function getValidSqlCountries($entry, $table): string
    {
        $iso2       = $entry['iso2'];
        $iso3       = $entry['iso3'];
        $titleDE    = $entry['title_de'];
        $titleEN    = $entry['title_en'];
        $titleFR    = $entry['title_fr'];
        $titleIT    = $entry['title_it'];
        $postData   = $entry['title_post_sort'];
        $isDeleted  = $entry['is_deleted'];
        $titleChangedDE = $entry['title_de_changed'];
        $isNew = $entry['is_new_entry'];


        $condition = SQLGenerator::buildCondition('iso', $iso2);
        $refConditions = $this->buildReferenceConditions($iso2);
        $sql = "";

        if ($isDeleted) {
            $condition .= " AND " . SQLGenerator::buildCondition('title_de', $titleDE);
            $sql .= $this->generateDeletedStatements($table, $condition, $refConditions);
        } elseif ($titleChangedDE && !$isNew) {
            $updateFields = [
                'iso3'              => $iso3,
                'title_de'          => $titleDE,
                'title_post_sort'   => $postData,
                'updated_at'        => date('Y-m-d H:i:s')
            ];
            $refInsert = [
                'reference_code'    => $iso2,
                'title_d'           => $titleDE,
            ];

            $sql .= $this->generateUpdateDeletedStatements($table, $updateFields, $condition);
            $sql .= SQLGenerator::sqlMultiIndividualInsertStatements('sys_reference_data', $refInsert) . PHP_EOL;
        } elseif ($isNew) {
            $insertData = [
                'iso2'              => $iso2,
                'iso3'              => $iso3,
                'title_de'          => $titleDE,
                'title_en'          => $titleEN,
                'title_fr'          => $titleFR,
                'title_it'          => $titleIT,
                'is_european_union' => 0,
                'is_european'       => 0,
                'price_code_post_urgent'    => 0,
                'title_post_sort'   => $postData,
                'created_at'        => date('Y-m-d H:i:s'),
                'updated_at'        => date('Y-m-d H:i:s')
            ];
            $refInsert = [
                'reference_code'    => $iso2,
                'title_d'           => $titleDE,
                'title_e'           => $titleEN,
                'title_f'           => $titleFR,
                'title_i'           => $titleIT
            ];
            $sql .= $this->generateNewEntryStatements($table, $insertData, $condition, $refConditions);
            $sql .= SQLGenerator::sqlMultiIndividualInsertStatements('sys_reference_data', $refInsert) . PHP_EOL;
        } else {
            $updateFields = [
                'iso3'              => $iso3,
                'title_post_sort'   => $postData,
                'updated_at'        => date('Y-m-d H:i:s')
            ];
            $sql .= $this->generateUpdateStatements($table, $updateFields, $condition);
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