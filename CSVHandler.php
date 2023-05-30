<?php

namespace kateland;
require_once 'Arrays.php';

class CSVHandler extends FileHandler
{
    /**
     * Read CSV file to array
     * @param $filename
     * @return array
     */
    private function csvToArray($filename): array
    {
        $file = self::openReadFile($filename);

        while (!feof($file)) {
            $csvArr[] = fgetcsv($file, 255, ',');
        }

        self::closeFile();

        return $csvArr;
    }

    /**
     * Get how headers from CSV as Array
     * @param $filename
     * @return array
     */
    private function rowHeadersToKeys($filename): array
    {
        $data = $this->csvToArray($filename);
        $keys = Arrays::getArrayValues($data[0]);
        foreach ($keys as $key) {
            $key = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $key);
            $fixedKeys[] = $key;
        }

        return $fixedKeys;
    }

    /**
     * Get data without array of keys
     * @param $filename
     * @return array
     */
    private function rowData($filename): array
    {
        $data = $this->csvToArray($filename);
        Arrays::shiftArray($data);
        return $data;
    }

    /**
     * Get data with row headers as keys in nested array
     * @param $filename
     * @return array
     */
    public function getDataWithKeys($filename): array
    {
        $keys = $this->rowHeadersToKeys($filename);
        $data = $this->rowData($filename);
        $arr = [];
        foreach ($data as $entry) {
            $entry = preg_replace('/(?<=[a-zA-Z])\'(?=[a-zA-Z]|[^\u0000-\u007F]|[À-ÿ])/', '\'', $entry);
            $newEntry = Arrays::combineArrays($keys, $entry);
            $arr[] = $newEntry;
        }
        array_shift($arr);
        return $arr;
    }
}