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


*/