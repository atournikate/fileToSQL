<?php

namespace file2sql;

class CSVHandler extends FileHandler
{
    /**
     * Read CSV file to array
     * @param $filename
     * @return array
     */
    private function csvToArray($filename): array
    {
        $csvArr = [];

        $file = fopen($filename, 'r');
        if ($file === false) {
            throw new \Exception("Unable to open file: $filename");
        }

        while( ($content = fgets($file)) !== false ) {
            $content = trim($content);
            $content = preg_replace("/^[\pZ\pC]+|[\pZ\pC]+$/u", "", $content);
            $row = str_getcsv($content, ", ");
            $row = array_map(function($value) {
                return trim($value);
            }, $row);

            $csvArr[] = $row;
        }

        fclose($file);
        return $csvArr;
    }

    public function getFormattedData($filename) {
        $data = $this->csvToArray($filename);
        $ret = [];

        if (!empty($data)) {
            $keys = $data[0];

            $num = count($data);

            for ($i = 1; $i < $num; $i++) {
                $values = $data[$i];
                $entry = array_combine($keys, $values);
                $ret[] = $entry;
            }
        }

        return $ret;
    }

}