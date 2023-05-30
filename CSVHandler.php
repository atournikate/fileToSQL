<?php

namespace kateland;
require_once 'Arrays.php';

class CSVHandler extends FileHandler
{

    private function csvToArray($filename)
    {
        $file = self::openReadFile($filename);

        while (!feof($file)) {
            $csvArr[] = fgetcsv($file, 255, ',');
        }

        self::closeFile();

        return $csvArr;
    }

    private function rowHeadersToKeys($filename)
    {
        $data = $this->csvToArray($filename);
        $keys = Arrays::getArrayValues($data[0]);
        foreach ($keys as $key) {
            $key = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $key);
            $fixedKeys[] = $key;
        }

        return $fixedKeys;
    }

    private function rowData($filename)
    {
        $data = $this->csvToArray($filename);
        Arrays::shiftArray($data);
        return $data;
    }

    public function getDataWithKeys($filename)
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