<?php (defined('BASEPATH')) or exit('No direct script access allowed');

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
    public function __construct($isRun = FALSE)
    {
        parent::__construct($isRun);
    }

    public function show_404 ($page = '', $log_error = TRUE)
    {

        echo Modules::run('comum/index');
        die();

    }

}
