<?php

namespace kateland;

class FileImporter
{
    private $filename;
}


class File
{



    public function __construct($filename, $table)
    {
        $this->filename = $filename;
        $this->table = $table;
    }

    public function run()
    {
        $this->test();
    }

    private function csvToArray()
    {
        $file = fopen($this->filename, 'r');

        while (!feof($file)) {
            $csvArr[] = fgetcsv($file, 255, ',');
        }

        fclose($file);

        return $csvArr;
    }

    private function rowHeadersToKeys()
    {
        $data = $this->csvToArray();
        $keys = array_values($data[0]);
        foreach ($keys as $key) {
            $key = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $key);
            $fixedKeys[] = $key;
        }

        return $fixedKeys;
    }

    private function rowData()
    {
        $data = $this->csvToArray();
        array_shift($data);
        return $data;
    }

    private function getDataWithKeys()
    {
        $keys = $this->rowHeadersToKeys();
        $data = $this->rowData();
        $arr = [];
        foreach ($data as $entry) {
            $entry = preg_replace('/(?<=[a-zA-Z])\'(?=[a-zA-Z]|[^\u0000-\u007F]|[À-ÿ])/', '\'', $entry);
            $newEntry = array_combine($keys, $entry);
            $arr[] = $newEntry;
        }

        return $arr;
    }

    private function createDeleteStatement($data)
    {
        $titleDE = $data['title_de_changed'];
        $deleted = $data['is_deleted'];
        $new = $data['is_new_entry'];
        $iso2 = reset($data);

        $pre = "DELETE FROM " . $this->table . " WHERE ";

        if (!$new && !$deleted && !$titleDE) {
            return;
        }

        if ($deleted) {
            $sql = $pre . "iso2= '" . $iso2;
            $sql .= "' AND title_de = '" . $data['title_de'];
            $sql .= "';  ";
        } else {
            $sql = $pre . "iso2= '" . $iso2;
            $sql .= "';  ";
        }

        return $sql;
    }

    private function selectMeta($data)
    {
        $unwanted = [
            'title_en',
            'title_fr',
            'title_it',
        ];
        foreach ($unwanted as $item) {
            unset($data[$item]);
        }
        return $data;
    }

    private function getISO3Only($data)
    {
        $unwanted = [
            'iso2',
            'title_de',
            'title_en',
            'title_fr',
            'title_it',
            'title_post_sort'
        ];

        foreach ($unwanted as $item) {
            unset($data[$item]);
        }

        return $data;
    }

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

    private function test()
    {
        $arr = $this->getDataWithKeys();
        $ret = "";
        foreach ($arr as $country) {
            $delete = $this->createDeleteStatement($country);
            $insert = $this->createInsertStatement($country);
            $ret .= $delete . $insert;
        }

        print_r($ret);
        exit;

        return $ret;
    }

}

$file = new File('countries.csv', 'orm_country');
$file->run();
