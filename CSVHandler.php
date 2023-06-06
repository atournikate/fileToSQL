<?php

namespace file2sql;

use Exception;

class CSVHandler
{
    /**
     * Read CSV file to array
     * @param $filename
     * @return array
     * @throws Exception
     */
    private function csvToArray($filename): array
    {
        $csvArr = [];

        $file = fopen($filename, 'r');
        if ($file === false) {
            throw new Exception("Unable to open file: $filename");
        }

        while( ($content = fgets($file)) !== false ) {
            $content = trim($content);
            //remove whitespaces or null spaces
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

    /**
     * Format row headers into array keys for data
     * @param $filename
     * @return array
     * @throws Exception
     */
    public function getFormattedData($filename): array
    {
        $data = $this->csvToArray($filename);
        $ret = [];

        if (!empty($data)) {
            $keys = $data[0];

            $num = count($data);

            for ($i = 1; $i < $num; $i++) {
                $values = $data[$i];
                $entry = array_combine($keys, $values);

                $entry['created_at'] = date('Y-m-d H:i:s');
                $entry['updated_at'] = date('Y-m-d H:i:s');

                $ret[] = $entry;
            }
        } else {
            throw new Exception("No data in file");
        }

        return $ret;
    }

}