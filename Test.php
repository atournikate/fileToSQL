<?php
namespace kateland;

require_once 'FileHandler.php';
require_once 'CSVHandler.php';
require_once 'SQLGenerator.php';

use kateland\{FileHandler, CSVHandler, SQLGenerator};

class Test {
    public function run($filename, $table)
    {
        FileHandler::setFilename($filename);
        $csv = new CSVHandler();
        $data = $csv->getDataWithKeys($filename);
        foreach ($data as $entry) {
            $sql[] = $this->getValidSqlCountries($entry, $table);
        }
        print_r($sql);
    }

    public function getSingleKeyValuePair($entry, $singleKey) {
        foreach ($entry as $key => $value) {
            if ($key === $singleKey) {
                $ret[$singleKey] = $value;
                return $ret;
            }
        }
    }

    public function getValidSqlCountries($entry, $table) {
        $sqlGen = new SQLGenerator($table, $entry);
        $titleChangedDE = $entry['title_de_changed'];
        $isDeleted = $entry['is_deleted'];
        $isNew = $entry['is_new_entry'];

        $iso2       = $entry['iso2'];
        $iso3       = $entry['iso3'];
        $titleDE    = $entry['title_de'];
        $postData   = $entry['title_post_sort'];

        $sqlGen->addCondition('iso2', $iso2);
        $sqlGen->addCondition('title_de', $titleDE);
        if ($isDeleted) {
            $sql = $sqlGen->sqlDeleteStatement();
        } elseif ($titleChangedDE && !$isNew) {
            $updateFields = [
                'iso3'              => $iso3,
                'title_de'          => $titleDE,
                'title_post_data'   => $postData
            ];
            $sql = $sqlGen->sqlUpdateStatement($updateFields);
            //update title_de, post, iso3
        } elseif ($isNew) {
            $sql = $sqlGen->sqlDeleteStatement();
            $sql .= "; " . $sqlGen->sqlInsertStatement();
        } else {
            $updateFields = [
                'iso3'              => $iso3
            ];
            $sql = $sqlGen->sqlUpdateStatement($updateFields);
            //update iso3
        }
        return $sql;
    }
/*

    private function createInsertStatement($data)
    {
        $titleDE = $data['title_de_changed'];
        $deleted = $data['is_deleted'];
        $new = $data['is_new_entry'];
        $meta = array_slice($data, 0, 7);

        $keys = implode(", ", array_keys($meta));
        $values = implode("', '", array_values($meta));

        $selectMeta = $this->selectMeta($meta);
        $selectKeys = implode(", ", array_keys($selectMeta));
        $selectValues = implode("', '", array_values($selectMeta));

        $iso3Only = $this->getISO3Only($meta);
        $iso3Key = implode('', array_keys($iso3Only));
        $iso3Value = implode('', array_values($iso3Only));

        $pre = "INSERT INTO " . $this->table . " (";
        if ($deleted) {
            return;
        }

        if (!$new && !$titleDE) {
            $sql = $pre . "$iso3Key ) VALUES ('";
            $sql .= $iso3Value;
        } elseif ($titleDE && !$new) {
            $sql = $pre . "$selectKeys ) VALUES ('";
            $sql .= $selectValues;
        } else {
            $sql = $pre . "$keys ) VALUES ('";
            $sql .= $values;
        }
        $sql .= "'); ";

        return $sql;
    }

*/

}

$filename = 'countries.csv';
$table = 'orm_country';

$test = new Test();
$test->run($filename, $table);