<?php

namespace file2sql;

class FileHandler
{

    private static $file;

    /**
     * @param $filename
     * @return mixed
     */
    public static function setFilename($filename) {
        return self::$file = $filename;
    }

    /**
     * Open and read file
     * @param $filename
     * @return false|resource
     */
    public static function openReadFile($filename) {
        return self::$file = fopen($filename, 'r');
    }

    /**
     * Close file
     * @return bool
     */
    public static function closeFile() {
        return fclose(self::$file);
    }
}

