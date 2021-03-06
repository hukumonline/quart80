<?php
/**
 * Utility methods for working with arrays.
 *
 * @package Pandamp_Util
 * @subpackage Util
 * @category Util
 *
 */
class Pandamp_Util_Array  {

    /**
     * Enforce static behavior.
     */
    private function __construct()
    {

    }

    /**
     * Applies the key/value pairs from array 2 to array 1.
     * Overwrites existing keys.
     *
     * @param array $array1
     * @param array $array2
     *
     */
    public static function apply(Array &$array1, Array $array2)
    {
        self::_apply($array1, $array2, false);
    }

    /**
     * Applies the key/value pairs from array 2 to array 1, if, and only
     * if the key for the value exists in $array1.
     *
     * @param array $array1
     * @param array $array2
     *
     */
    public static function applyStrict(Array &$array1, Array $array2)
    {
        self::_apply($array1, $array2, true);
    }

    /**
     * Applies the key/value pairs from array 2 to array 1, if, and only
     * if this key is not already set in array 1.
     *
     * @param array $array1
     * @param array $array2
     *
     */
    public static function applyIf(Array &$array1, Array $array2)
    {
        self::_apply($array1, $array2, false, true);
    }

    private static function _apply(Array &$array1, Array $array2, $strict = false, $preserveExisting = false)
    {
        foreach ($array2 as $key => $value) {
            if ($strict && !array_key_exists($key, $array1)) {
                continue;
            }

            if ($preserveExisting && isset($array1[$key])) {
                continue;
            }

            $array1[$key] = $value;
        }
    }

    /**
     * Camelizes the keys of an assoziative array and returns
     * the array with the new keys, if the second parameter is set to "true".
     * Otherwise, the reference to the passed argument will be used and the array
     * will be changed directly.
     *
     * @param Array $array The associative array to camelize the keys.
     * @param boolean $return "true" for return a copy of the array with
     * the camelized keys, otherwise false
     *
     * @return Array The new array with the keys camelized or null, if the
     * reference is used
     *
     * @deprecated use camelizeKeys2 instead
     */
    public static function camelizeKeys(Array &$array, $return = false)
    {
        if ($return !== true) {
            $keys = array_keys($array);
            $values = array_values($array);
            foreach ($keys as $k => $v) {
                $camelKey = '_' . str_replace('_', ' ', strtolower($v));
                $camelKey = ltrim(str_replace(' ', '', ucwords($camelKey)), '_');
                $keys[$k] = $camelKey;
            }

            $array = array_combine($keys, $values);
            return null;
        } else {
            $data = array();
            foreach ($array as $key => $value) {
                $camelKey = '_' . str_replace('_', ' ', strtolower($key));
                $camelKey = ltrim(str_replace(' ', '', ucwords($camelKey)), '_');

                $data[$camelKey] = $value;
            }

            return $data;
        }
    }

    /**
     * Underscores the keys of an assoziative array (if camelized) and returns
     * the array with the new keys, if the second parameter is set to "true".
     * Otherwise, the reference to the passed argument will be used and the array
     * will be changed directly.
     *
     * @param Array $array The associative array to underscore the keys.
     * @param boolean $return "true" for return a copy of the array with
     * the underscored keys, otherwise false
     *
     * @return Array The new array with the keys underscored or null, if the
     * reference is used
     */
    public static function underscoreKeys(Array &$array, $return = false)
    {
        if ($return !== true) {

            $keys   = array_keys($array);
            $values = array_values($array);
            foreach ($keys as $k => $v) {
                $keys[$k] = strtolower(preg_replace('/([a-z])([A-Z])/', "$1_$2", $v));
            }

            $array = array_combine($keys, $values);
            return null;
        } else {
            $data = array();

            foreach ($array as $key => $value) {
                $data[strtolower(preg_replace('/([a-z])([A-Z])/', "$1_$2", $key))] = $value;
            }

            return $data;
        }
    }

    /**
     * Checks wether the passed argument is an associative array.
     *
     * @param array $array The array to check for associative keys
     *
     * @return bollean "true", if the array is associative, otherwise
     * "false"
     */
    public static function isAssociative(array $array)
    {
        return count($array) !== array_reduce(
            array_keys($array),
            array('Pandamp_Util_Array', '_isAssocCallback'),
            0
        );
    }

    /**
     * Callback for array_reduce-function used in isAssociative
     */
    private static function _isAssocCallback($a, $b)
    {
        return $a === $b ? $a + 1 : 0;
    }

}