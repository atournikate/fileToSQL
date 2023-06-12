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

        // Create a new Spreadsheet object
        $spreadsheet = new Spreadsheet();

        // Select the active sheet
        $sheet = $spreadsheet->getActiveSheet();

        // Write the headers
        $sheet->setCellValue('A1', 'nome');
        $sheet->setCellValue('B1', 'codice');

        // Write the data
        $row = 2;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $item['nome']);
            $sheet->setCellValue('B' . $row, $item['codice']);
            $row++;
        }

        // Create a new Xlsx Writer object
        $writer = new Xlsx($spreadsheet);

        // Save the spreadsheet to a file
        $writer->save($xlsxFile);
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
$jsonFile = 'it_plz.json';
$xlsxFile = 'IT.xlsx';
$converter->convertJSONtoXLSX($jsonFile, $xlsxFile);
