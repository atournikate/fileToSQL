<?php

namespace file2sql;
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CSVtoXLSXConverter
{
    protected $csvFilePath;
    protected $xlsxFilePath;

    public function __construct($csvFilePath, $xlsxFilePath)
    {
        $this->csvFilePath = $csvFilePath;
        $this->xlsxFilePath = $xlsxFilePath;
    }

    public function convert()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Read the CSV file
        $csvData = file($this->csvFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        // Get the headers from the first line
        $headers = str_getcsv($csvData[0], ';');

        // Set the headers as the first row in the spreadsheet
        $sheet->fromArray($headers, null, 'A1');

        // Remove the first line from the CSV data
        unset($csvData[0]);

        // Set the remaining CSV data to the spreadsheet
        $row = 2;
        foreach ($csvData as $csvLine) {
            $rowData = str_getcsv($csvLine, ';');
            $sheet->fromArray($rowData, null, 'A' . $row);
            $row++;
        }

        // Save the spreadsheet to XLSX file
        $writer = new Xlsx($spreadsheet);
        $writer->save($this->xlsxFilePath);
    }
}

$csvFilePath = 'laposte_hexasmal.csv';
$xlsxFilePath = 'laposte.xlsx';

$converter = new CSVtoXLSXConverter($csvFilePath, $xlsxFilePath);
$converter->convert();
