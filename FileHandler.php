<?php

namespace kateland;

class FileHandler
{

    private static $file;

    public static function setFilename($filename) {
        return self::$file = $filename;
    }

    public static function openReadFile($filename) {
        return self::$file = fopen($filename, 'r');
    }

    public static function closeFile() {
        return fclose(self::$file);
    }
}

