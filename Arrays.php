<?php

namespace kateland;

class Arrays
{
    /**
     * Remove first element from array
     * @param $array
     * @return void
     */
    public static function shiftArray($array) {
        return array_shift($array);
    }

    public static function getArrayKeys($array) {
        return array_keys($array);
    }

    public static function getArrayValues($array) {
        return array_values($array);
    }

    public static function combineArrays($keyArr, $valArr) {
        return array_combine($keyArr, $valArr);
    }

    public static function getArrayRandom($array) {
        return array_rand($array, 1);
    }


}