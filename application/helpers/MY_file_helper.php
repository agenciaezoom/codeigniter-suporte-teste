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
 * CodeIgniter File Helpers
 *
 * @package     CodeIgniter
 * @subpackage  File
 * @category    Helpers
 * @author      Ramon Barros <ramon@ezoom.com.br>
 * @link
 */

// ------------------------------------------------------------------------

/**
 * Delete File
 *
 * Delete file contained in the directory path.
 *
 * @access  public
 * @param   string  path to file
 * @return  bool
 */
if (!function_exists('delete_file')) {
    function delete_file($file)
    {
        if (! file_exists($file)) {
            return false;
        } else {
            return unlink($file);
        }
    }
}
