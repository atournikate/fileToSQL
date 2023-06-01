<?php

/*public function getSingleKeyValuePair($entry, $singleKey) {
    foreach ($entry as $key => $value) {
        if ($key === $singleKey) {
            $ret[$singleKey] = $value;
            return $ret;
        }
    }
}*/
/*private function createDeleteStatement($data)
{
    $titleDE = $data['title_de_changed'];
    $deleted = $data['is_deleted'];
    $new = $data['is_new_entry'];
    $iso2 = reset($data);

    $pre = "DELETE FROM " . $this->table . " WHERE ";

    if (!$new && !$deleted && !$titleDE) {
        return;
    }

    if ($deleted) {
        $sql = $pre . "iso2= '" . $iso2;
        $sql .= "' AND title_de = '" . $data['title_de'];
        $sql .= "';  ";
    } else {
        $sql = $pre . "iso2= '" . $iso2;
        $sql .= "';  ";
    }

    return $sql;
}

private function selectMeta($data)
{
    $unwanted = [
        'title_en',
        'title_fr',
        'title_it',
    ];
    foreach ($unwanted as $item) {
        unset($data[$item]);
    }
    return $data;
}

private function getISO3Only($data)
{
    $unwanted = [
        'iso2',
        'title_de',
        'title_en',
        'title_fr',
        'title_it',
        'title_post_sort'
    ];

    foreach ($unwanted as $item) {
        unset($data[$item]);
    }

    return $data;
}

private function createInsertStatement($data)
{
    $titleDE = $data['title_de_changed'];
    $deleted = $data['is_deleted'];
    $new = $data['is_new_entry'];
    $meta = array_slice($data, 0, 7);

    $keys = implode(", ", array_keys($meta));
    $values = implode("', '", array_values($meta));

    $selectMeta = $this->selectMeta($meta);
    $selectKeys = implode(", ", array_keys($selectMeta));
    $selectValues = implode("', '", array_values($selectMeta));

    $iso3Only = $this->getISO3Only($meta);
    $iso3Key = implode('', array_keys($iso3Only));
    $iso3Value = implode('', array_values($iso3Only));

    $pre = "INSERT INTO " . $this->table . " (";
    if ($deleted) {
        return;
    }

    if (!$new && !$titleDE) {
        $sql = $pre . "$iso3Key ) VALUES ('";
        $sql .= $iso3Value;
    } elseif ($titleDE && !$new) {
        $sql = $pre . "$selectKeys ) VALUES ('";
        $sql .= $selectValues;
    } else {
        $sql = $pre . "$keys ) VALUES ('";
        $sql .= $values;
    }
    $sql .= "'); ";

    return $sql;
}


    public function __construct($filename, $table)
    {
        $this->filename = $filename;
        $this->table = $table;
    }

    public function createSqlFile()
    {
        $this->test();
    }




    private function test()
    {
        $arr = $this->getDataWithKeys();
        $ret = "";
        foreach ($arr as $country) {
            $delete = $this->createDeleteStatement($country);
            $insert = $this->createInsertStatement($country);
            $ret .= $delete . $insert;
        }

        print_r($ret);
        exit;

        return $ret;
    }


    private function createDeleteStatement($data)
    {
        $titleDE = $data['title_de_changed'];
        $deleted = $data['is_deleted'];
        $new = $data['is_new_entry'];
        $iso2 = reset($data);

        $pre = "DELETE FROM " . $this->table . " WHERE ";

        if (!$new && !$deleted && !$titleDE) {
            return;
        }

        if ($deleted) {
            $sql = $pre . "iso2= '" . $iso2;
            $sql .= "' AND title_de = '" . $data['title_de'];
            $sql .= "';  ";
        } else {
            $sql = $pre . "iso2= '" . $iso2;
            $sql .= "';  ";
        }

        return $sql;
    }

    private function selectMeta($data)
    {
        $unwanted = [
            'title_en',
            'title_fr',
            'title_it',
        ];
        foreach ($unwanted as $item) {
            unset($data[$item]);
        }
        return $data;
    }

    private function getISO3Only($data)
    {
        $unwanted = [
            'iso2',
            'title_de',
            'title_en',
            'title_fr',
            'title_it',
            'title_post_sort'
        ];

        foreach ($unwanted as $item) {
            unset($data[$item]);
        }

        return $data;
    }

    private function createInsertStatement($data)
    {
        $titleDE = $data['title_de_changed'];
        $deleted = $data['is_deleted'];
        $new = $data['is_new_entry'];
        $meta = array_slice($data, 0, 7);

        $keys = implode(", ", array_keys($meta));
        $values = implode("', '", array_values($meta));

        $selectMeta = $this->selectMeta($meta);
        $selectKeys = implode(", ", array_keys($selectMeta));
        $selectValues = implode("', '", array_values($selectMeta));

        $iso3Only = $this->getISO3Only($meta);
        $iso3Key = implode('', array_keys($iso3Only));
        $iso3Value = implode('', array_values($iso3Only));

        $pre = "INSERT INTO " . $this->table . " (";
        if ($deleted) {
            return;
        }

        if (!$new && !$titleDE) {
            $sql = $pre . "$iso3Key ) VALUES ('";
            $sql .= $iso3Value;
        } elseif ($titleDE && !$new) {
            $sql = $pre . "$selectKeys ) VALUES ('";
            $sql .= $selectValues;
        } else {
            $sql = $pre . "$keys ) VALUES ('";
            $sql .= $values;
        }
        $sql .= "'); ";

        return $sql;
    }


    /**
     * Get how headers from CSV as Array
     * @param $filename
     * @return array
     */
/*private function rowHeadersToKeys($filename): array
{
    $data = $this->csvToArray($filename);
    $keys = array_values($data[0]);
    foreach ($keys as $key) {
        $key = preg_replace("/[\x{200B}-\x{200D}\x{FEFF}]/u", "", $key);
        $fixedKeys[] = $key;
    }

    return $fixedKeys;
}

/**
 * Get data without array of keys
 * @param $filename
 * @return array
 */
/*private function rowData($filename): array
{
    $data = $this->csvToArray($filename);
    array_shift($data);
    return $data;
}*/

/**
 * Get data with row headers as keys in nested array
 * @param $filename
 * @return array
 */
/*public function getDataWithKeys($filename): array
{
    $keys = $this->rowHeadersToKeys($filename);
    $data = $this->rowData($filename);
    $arr = [];
    foreach ($data as $entry) {
        $entry = preg_replace("/(?<=[a-zA-Z])'(?=[a-zA-Z]|[^\u{0000}-\u{007F}]|[À-ÿ])/", "\'", $entry);
        $newEntry = array_combine($keys, $entry);
        $arr[] = $newEntry;
    }
    array_shift($arr);
    return $arr;
}*/

/**
 * Get data with row headers as keys in nested array
 * @param $filename
 * @return array
 */
/*public function getDataWithoutLastKey($filename): array
{
    $keys = $this->rowHeadersToKeys($filename);
    $data = $this->rowData($filename);
    $arr = [];
    foreach ($data as $entry) {
        $newEntry = array_combine($keys, $entry);
        //$entry = preg_replace("/(?<=[a-zA-Z])'(?=[a-zA-Z]|[^\u{0000}-\u{007F}]|[À-ÿ])/", "\'", $entry);
        $arr[] = $newEntry;
    }
    return $arr;
}

*/