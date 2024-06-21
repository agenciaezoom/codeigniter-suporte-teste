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
 * Rename File
 *
 * Rename file contained in the directory path.
 *
 * @access  public
 * @param   string  path to file
 * @param   string  prefix
 * @param   string  suffix
 * @return  bool
 */
if (!function_exists('rename_file')) {
    function rename_file($file, $prefix = '', $suffix = '')
    {
        try {
            if (file_get_contents($file)) {
                $pathinfo = pathinfo($file);
                $origin = $pathinfo['dirname'] . DS . $pathinfo['filename'] . '.' . $pathinfo['extension'];
                $dest = $prefix . $pathinfo['dirname'] . DS . $pathinfo['filename'] . $suffix .'.'. $pathinfo['extension'];
                $rename = rename($origin, $dest);
                return !empty($rename);
            }
        } catch (Exception $e) {
            return false;
        }
    }
}

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
        try {
            if (file_get_contents($file)) {
                require APPPATH.DS.'config'.DS.'database'.EXT;

                $pathinfo = pathinfo($file);
                if (!empty($pathinfo)) {
                    if ($db[$active_group]['soft_delete']) {
                        $unlink = array_map(
                            'rename_file',
                            glob($pathinfo['dirname'] . DS . $pathinfo['filename'] . '.' . $pathinfo['extension']),
                            array(),
                            array('.'.$db[$active_group]['soft_delete_column'])
                        );
                    } else {
                        $unlink = array_map(
                            'unlink',
                            glob($pathinfo['dirname'] . DS . $pathinfo['filename'] . '.' . $pathinfo['extension'])
                        );
                    }
                    return !empty($unlink);
                }
            }
        } catch (Exception $e) {
            return false;
        }
    }
}
