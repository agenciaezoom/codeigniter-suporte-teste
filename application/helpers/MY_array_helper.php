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

    public function array_order_by(&$array = array(), $key = 'order_by', $sort = SORT_ASC)
    {
        $sort = new _array_order_by($array, $key, $sort);
    }

}
