<?php

namespace App\Tool\Compet\Core;

class DkArrays {
    /**
     * Sort with comparator, `keys` will be preserved.
	 * Note: this is different with `usort()` which remove keys from given array.
	 * 
	 * @return boolean True if succeed. Otherwise False.
     */
    public static function sort(&$arr, $comparator) {
        return uasort($arr, $comparator);
    }
    
    /**
     * Merge 2 arrays with given action.
     * @param array $arr1 Array
     * @param array $arr2 Array
     * @return array
     */
    public static function mergeArrays($arr1, $arr2, $merge_action) {
        $result = [];
        $all_keys = array_merge(array_keys($arr1), array_keys($arr2));

        foreach ($all_keys as $key) {
            $val1 = $arr1[$key] ?? null;
            $val2 = $arr2[$key] ?? null;
            $val1_is_array = is_array($val1);
            $val2_is_array = is_array($val2);
            
            if ($val1_is_array && $val2_is_array) {
                $result[$key] = self::mergeArray($val1, $val2, $merge_action);
            }
            else if ($val1_is_array) {
                $result[$key] = self::mergeArray($val1, [], $merge_action);
            }
            else if ($val2_is_array) {
                $result[$key] = self::mergeArray([], $val2, $merge_action);
            }
            else {
                $result[$key] = $merge_action($val1, $val2);
            }
        }

        return $result;
    }
    
	/**
	 * Trims array or string deeply.
	 * 
	 * @param array|string Any type but should be array or string.
	 * @return void
	 */
    public static function trims(&$data) {
        if (is_array($data)) {
            foreach ($data as &$item) {
                self::trims($item);
            }
        }
        else if (is_string($data)) {
            $data = trim($data);
        }
    }
	
	/**
	 * @param array $rows Think it as a table which has header contains given `key`.
	 * @param string $key Can specify any column.
     * @return array Values at given key.
     */
    public static function column($rows, $key) {
        $result = [];
        foreach ($rows as $row) {
            $result [] = $row[$key];
        }
        return $result;
    }

    /**
     * Get a mapp of value1-value2 between given 2 keys.
	 * 
	 * @param array $rows Think it as a table which has header contains given `key`.
	 * @param string $key1 Should specify string or int column.
	 * @param string $key2 Can specify any column.
     * @return array
     */
    public static  function column2(&$arr, $key1, $key2) {
        $res = [];
        foreach ($arr as $item) {
            $res[$item[$key1]] = $item[$key2];
        }
        return $res;
    }
}