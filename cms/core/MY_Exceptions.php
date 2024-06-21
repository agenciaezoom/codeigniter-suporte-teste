<?php (defined('BASEPATH')) or exit('No direct script access allowed');

/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package CodeIgniter
 * @author  ExpressionEngine Dev Team
 * @copyright  Copyright (c) 2006, EllisLab, Inc.
 * @license http://codeigniter.com/user_guide/license.html
 * @link http://codeigniter.com
 * @since   Version 2.1.4
 * @filesource
 */

// --------------------------------------------------------------------

/**
 * CodeIgniter MY_Exceptions
 *
 * Página de erro
 *
 * @package     CodeIgniter
 * @author      Ezoom
 * @subpackage  Exceptions
 * @category    Exceptions
 * @link        http://ezoom.com.br
 * @copyright  Copyright (c) 2008, Ezoom
 * @version 1.0.0
 *
 */
class MY_Exceptions extends CI_Exceptions
{

    /**
     * Retorna a página de erros caso a rota não exista.
     * @author Ezoom [ezoom@ezoom.com.br]
     * @date   2015-07-02
     * @param  string  $page
     * @param  boolean $log_error
     * @return void
     */
    public function show_404($page = '', $log_error = true)
    {
        $CI =& get_instance();
        $CI->load->helper('language');
        $CI->lang->load('default');

        $vars = array(
            'lang' => $CI->lang->lang()
        );
        echo $CI->load->view('error_404.php', $vars, true);

        exit;
    }
}
