<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Util;

/**
 * A class with utility functions for arrays.
 *
 * @author Phillip Look <plook@brainbits.net>
 */
class ArrayUtil
{
    /**
     * Extract one column of an 2-dim array.
     *
     * @param array $src       2-dim array
     * @param mixed $column    name or index of column to extract
     * @param bool  $skip      (Optional) skip not existing values, default: add null
     * @param bool  $skipEmpty (Optional) skip not empty values, default: add null
     *
     * @return array
     */
    public function column(array $src, $column, $skip = false, $skipEmpty = false)
    {
        $result = array();

        // process each row
        foreach ($src as $key => $row) {
            // if current row is an array and the specified column exists
            // store column value otherwise null
            if (is_array($row) && array_key_exists($column, $row)) {
                if (!$skipEmpty || !empty($row[$column])) {
                    $result[$key] = $row[$column];
                }
            } else {
                // skip not existing values ...
                if (!$skip) {
                    // ... or add null
                    $result[$key] = null;
                }
            }
        }

        return $result;
    }

    /**
     * Get the value in the array or a default value if key not exists.
     *
     * @param array          $array
     * @param integer|string $key
     * @param mixed          $default
     *
     * @return mixed
     */
    public function get(array $array, $key, $default = null)
    {
        $value = array_key_exists($key, $array)
            ? $array[$key]
            : $default;

        return $value;
    }

    /**
     * Group a 2-dim array by a set of columns.
     *
     * @param array            $array   2-dim array
     * @param int|string|array $columns index of the group-by column(s)
     *
     * @return array
     */
    public function groupBy($array, $columns)
    {
        // ensure $columns parameter is array
        $columns = (array) $columns;

        // get first group-by column
        $col = array_shift($columns);

        $result = array();
        foreach ($array as $row) {
            $key = (string) is_object($row) ? $row->$col : $row[$col];
            if (!isset($result[$key])) {
                $result[$key] = array();
            }

            $result[$key][] = $row;
        }

        // do subsequent group by calls
        if (count($columns)) {
            foreach (array_keys($result) as $key) {
                $result[$key] = $this->groupBy($result[$key], $columns);
            }
        }

        return $result;
    }

    /**
     * Convert associative array <key> => <value> to indexed array [<key>, <value>].
     *
     * @param array $input
     *
     * @return array
     */
    public function keyToValue(array $input)
    {
        $output = array();

        foreach ($input as $key => $value) {
            $output[] = array($key, $value);
        }

        return $output;
    }

    /**
     * Search for a data in a multidimensional array
     *
     * Example:
     * $parents = array();
     * $parents[] = array('date'=>1320883200, 'uid'=>3);
     * $parents[] = array('date'=>1320883200, 'uid'=>5);
     * $parents[] = array('date'=>1318204800, 'uid'=>5);
     * echo multidimensionalSearch($parents, array('date'=>1320883200, 'uid'=>5)); // true
     *
     * @param array  $parents
     * @param string $searched
     *
     * @return mixed
     */
    public function multidimensionalSearch($parents, $searched)
    {
        if (empty($searched) || empty($parents)) {
            return false;
        }

        foreach ($parents as $key => $value) {
            $exists = true;
            foreach ($searched as $skey => $svalue) {
                $exists = $exists && isset($parents[$key][$skey]) && $parents[$key][$skey] == $svalue;
            }

            if ($exists) {
                return true;
            }
        }

        return false;
    }

    /**
     * Search in multidimensional array and return key
     *
     * @param array $needle   the searched data
     * @param array $haystack the array to search in
     *
     * @return boolean|integer
     */
    public function arraySearch($needle, $haystack)
    {
        if (empty($needle) || empty($haystack)) {
            return false;
        }

        foreach ($haystack as $key => $value) {
            $exists = 0;
            foreach ($needle as $nkey => $nvalue) {
                if (!empty($value[$nkey]) && $value[$nkey] == $nvalue) {
                    $exists = 1;
                } else {
                    $exists = 0;
                }
            }

            if ($exists) {
                return $key;
            }
        }

        return false;
    }
}
