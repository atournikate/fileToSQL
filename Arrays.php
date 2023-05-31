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
       array_shift($array);
       return $array;
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

    public static function removeLastElement($array) {
        array_pop($array);
        return $array;
    }

    /**
     * Combine two arrays, values of the first becoming the keys for the second
     * @param $keyArr
     * @param $valArr
     * @return array|false
     */
    public static function combineArrays(array $keyArr, array $valArr): array 
    {
        return array_combine($keyArr, $valArr);
    }

}