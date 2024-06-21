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
 * CodeIgniter Auth Class
 *
 * Classe para controle do login e permissões
 *
 * @package    CodeIgniter
 * @subpackage  Auth
 * @category    Libraries
 * @author      Fábio Augustin Neis
 * @link
 */
class Auth
{
    private $auth;

    public function __construct()
    {
        $this->ci = &get_instance();
        $this->ci->load->helper('url');

        //Recupera da sessão os dados do usuário
        $this->auth = new stdClass();
        $this->auth->company = isset($_SESSION['company']['id']) ? $_SESSION['company']['id'] : 1;
    }

     /**
     * Retorna o valor informado da sessão
     * @author Fabio Neis [fabio@ezoom.com.br]
     * @date   2015-06-15
     * @param  [string] $var
     * @return [mixed]
     */
    public function data($var)
    {
        $res = false;
        if (isset($this->auth->{$var})) {
            $res = $this->auth->{$var};
        }

        return $res;
    }
}
