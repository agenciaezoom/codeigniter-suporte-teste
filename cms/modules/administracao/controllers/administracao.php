<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Administracao extends MY_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index ($pg = 1, $path = '')
    {
        redirect('administracao/usuarios');
    }

}

/* End of file administracao.php */
/* Location: ./application/controllers/administracao.php */