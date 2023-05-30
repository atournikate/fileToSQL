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

    /**
     * Get array keys as array
     * @param $array
     * @return array
     */
    public static function getArrayKeys($array): array
    {
        return array_keys($array);
    }

    /**
     * Get array values
     * @param $array
     * @return array
     */
    public static function getArrayValues($array): array
    {
        return array_values($array);
    }

    /**
     * Combine two arrays, values of the first becoming the keys for the second
     * @param $keyArr
     * @param $valArr
     * @return array|false
     */
    public static function combineArrays($keyArr, $valArr) {
        return array_combine($keyArr, $valArr);
    }

}