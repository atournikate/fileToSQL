<?php
include_once 'vendor/autoload.php';
use Shuchkin\SimpleXLSX;

class Excel
{
    private $filename;
    private $tableName;

    public function __construct($filename, $tableName) {
        $this->filename = $filename;
        $this->tableName = $tableName;
    }

    public function createArrayFromExcel($sheetName = null) {
        $xlsx = SimpleXLSX::parse($this->filename);
        //print_r($xlsx->sheetNames());exit;
        $sheetsArr = $xlsx->sheetNames();

        foreach ($sheetsArr as $key => $value) {
            if ($value == $sheetName) {
                $sheetIndex = $key;
            }
        }

        $header_values = $rows = [];
        foreach ($xlsx->rows($sheetIndex) as $key => $value) {
            if ($key == 0) {
                $header_values = $value;
                continue;
            }
            $rows[] = array_combine($header_values, $value);
        }
        return $rows;
    }

    public function getKeysAsArray($arr) {
        foreach ($arr as $entry) {
            $keys = array_keys($entry);

        }
        return $keys;
    }



    public function createSQLDeleteStatement($field, $value = null, $arr = null) {
        $pre = "DELETE FROM $this->tableName WHERE $field = ";
        if ($value) {
            $sql = $pre . $value;
        } else {
            $sql = $pre;
        }

        if ($arr) {
            foreach ($arr as $entity) {
                foreach ($entity as $key => $val) {
                    if ($key == $field) {
                        $sql .= "'" . $val . "'";
                        $sql .= "; " . $pre;
                    }
                }
            }
        }



        print_r($sql);


    }

}
$ex = new Excel("Countries_kme_2023-05-24.xlsx", 'orm_country');
$newEntries = $ex->createArrayFromExcel('new');
print_r($newEntries);exit;
$ex->createSQLDeleteStatement('iso2', 'AX');