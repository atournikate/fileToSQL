<?php

namespace file2sql;

require 'vendor/autoload.php';


use Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class JSONtoXLSXConverter
{
    public function convertJSONtoXLSX(string $jsonFile, string $xlsxFile)
    {
        if (!file_exists($jsonFile)) {
            throw new Exception("No data");
        }
        // Load the JSON file
        $jsonContents = file_get_contents($jsonFile);
        $data = json_decode($jsonContents, true);
        var_dump($data);
        exit;

    }

    /**
     * Generate the cell reference for the given column and row
     * @param int $col
     * @param int $row
     * @return string
     */
    private function generateCellReference(int $col, int $row): string
    {
        $letters = range('A', 'Z');
        $colRef = $letters[$col % 26];
        if ($col >= 26) {
            $colRef = $letters[intval($col / 26) - 1] . $colRef;
        }
        return $colRef . $row;
    }
}

// Usage
$converter = new JSONtoXLSXConverter();
$jsonFile = 'tmp/laposte_hexasmal.json';
$xlsxFile = 'tmp/laposte_hexasmal.xlsx';
$converter->convertJSONtoXLSX($jsonFile, $xlsxFile);
