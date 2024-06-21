<?php (defined('BASEPATH')) or exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package     CodeIgniter
 * @author      ExpressionEngine Dev Team
 * @copyright   Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license     http://codeigniter.com/user_guide/license.html
 * @link        http://codeigniter.com
 * @since       Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter Array Helpers
 *
 * @package     CodeIgniter
 * @subpackage  Helpers
 * @category    Helpers
 * @author      Ramon Barros - Ezoom
 * @link
 */

// ------------------------------------------------------------------------

/**
 * Array order by Asc/desc
 *
 * Ordena uma array ou objeto pela chave informada e o tipo de ordem.
 *
 * @access  public
 * @param   mixed
 * @return  void
 */
if (! function_exists('array_order_by')) {
    if (!class_exists('_array_order_by')) {
        class _array_order_by
        {
            public static $key;
            public function __construct(&$array = array(), $key = 'order_by', $sort = SORT_ASC)
            {
                static::$key = $key;
                switch ($sort) {
                    case SORT_ASC:
                        uasort($array, array($this, '_array_order_by_asc'));
                        break;
                    case SORT_DESC:
                        uasort($array, array($this, '_array_order_by_desc'));
                        break;
                }
            }

            private static function _array_order_by_asc($a, $b)
            {
                if (is_object($a) && is_object($b)) {
                    if (isset($a->{static::$key})) {
                        return ((int) $a->{static::$key} < (int) $b->{static::$key}) ? -1 : 1;
                    } else {
                        return 0;
                    }
                } elseif (is_array($a) && is_array($b)) {
                    if (isset($a[static::$key])) {
                        return ((int) $a[static::$key] < (int) $b[static::$key]) ? -1 : 1;
                    } else {
                        return 0;
                    }
                }
            }

            private static function _array_order_by_desc($a, $b)
            {
                if (is_object($a) && is_object($b)) {
                    if (isset($a->{static::$key})) {
                        return ((int) $a->{static::$key} > (int) $b->{static::$key}) ? -1 : 1;
                    } else {
                        return 0;
                    }
                } elseif (is_array($a) && is_array($b)) {
                    if (isset($a[static::$key])) {
                        return ((int) $a[static::$key] > (int) $b[static::$key]) ? -1 : 1;
                    } else {
                        return 0;
                    }
                }
            }
        }
    }

    function array_order_by(&$array = array(), $key = 'order_by', $sort = SORT_ASC)
    {
        $sort = new _array_order_by($array, $key, $sort);
    }

}

/* Busca item numa collection e retorna se item existe ou campo específico se passado
 * Usado para verificar se uma model possui outra relacionada ou pegar algum atributo
 * de uma model relacionada
 */
function in_object_collection($collection, $field, $value, $return_field = FALSE){
    $array_collection = objectToJson($collection);
    $array_filtered = array_column($array_collection, $field);

    if($return_field === FALSE){
        return in_array($value, $array_filtered);
    } else {
        $key = array_search($value, $array_filtered);

        if(array_key_exists($return_field, $array_collection[$key])){
            return $array_collection[$key][$return_field];
        } else {
            return FALSE;
        }
    }
}

function diff_array_objects($arr1, $arr2)
{
    if (is_array($arr1) && is_array($arr2)) {
        return array_udiff($arr1, $arr2, 'compare_objects');
    } else {
        return $arr1;
    }
}

function compare_objects($obj1, $obj2) {
    return  $obj1->id - $obj2->id;
}

function array_flatten($array)
{
    $return = array();
    foreach ($array as $key => $value) {
        if (is_array($value)){
            $return = array_merge($return, array_flatten($value));
        } else {
            $return[$key] = $value;
        }
    }

    return $return;
}

if (! function_exists('array_column')) {
    function array_column(array $input, $columnKey, $indexKey = null) {
        $array = array();
        foreach ($input as $value) {
            if ( !array_key_exists($columnKey, $value)) {
                trigger_error("Key \"$columnKey\" does not exist in array");
                return false;
            }
            if (is_null($indexKey)) {
                $array[] = $value[$columnKey];
            }
            else {
                if ( !array_key_exists($indexKey, $value)) {
                    trigger_error("Key \"$indexKey\" does not exist in array");
                    return false;
                }
                if ( ! is_scalar($value[$indexKey])) {
                    trigger_error("Key \"$indexKey\" does not contain scalar value");
                    return false;
                }
                $array[$value[$indexKey]] = $value[$columnKey];
            }
        }
        return $array;
    }
}