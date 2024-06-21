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
 * CodeIgniter Debug Helpers
 *
 * @package     CodeIgniter
 * @subpackage  Debug
 * @category    Helpers
 * @author      Ramon Barros - Ezoom
 * @link
 */

// ------------------------------------------------------------------------

/**
 * Dump
 *
 * Mostra um var_dump com <pre> ao valor passado
 *
 * @access  public
 * @param   mixed
 * @return  void
 */
if (!function_exists('d')) {
    function d($value)
    {
        echo "<pre>";
        var_export($value);
        echo "</pre>";
    }

}

/**
 * Dump Die
 *
 * Mostra um var_dump e die com <pre> ao valor passado
 *
 * @access  public
 * @param   mixed
 * @return  void
 */
if (!function_exists('dd')) {
    function dd($value)
    {
        echo "<pre>";
        var_dump($value);
        echo "</pre>";
        die;
    }

}

/**
 * Last Query
 *
 * Retorna a ultima query do banco executada
 *
 * @access  public
 * @return  void
 */
if (!function_exists('last_query')) {
    function last_query()
    {
        $CI = &get_instance();
        d($CI->db->last_query());
    }
}
