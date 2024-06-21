<?php (defined('BASEPATH')) or exit('No direct script access allowed');

class Login extends MY_Controller
{
    public function __construct()
    {
        $this->load->helper('cookie');
        parent::__construct();
    }

    public function index()
    {
        if ($this->auth->logged() == true) {
            redirect('home', 'location');
        }

        $this->template->set_template('login');
        $this->template
             ->add_css('css/login')
             ->add_js('js/login')
             ->set('title', SITE_NAME.' - Login')
             ->set_partial('header', '')
             ->set_partial('sidebar', '')
             ->set_partial('breadcrumb', '')
             ->set_partial('footer', '')
             ->build('login');
    }

    public function auth()
    {
        $cookie = get_cookie('try_login', TRUE);
        if($cookie <= 5){
            $post = $this->input->post();
            if ($this->auth->login($post['username'], $post['password'])) {
                $this->auth->refresh_data();
                delete_cookie('try_login');

                $url = site_url($this->auth->data('language_main').'/home/');

                $redirect = $this->session->userdata('redirect');
                if($redirect)
                {
                    $this->session->unset_userdata('redirect');
                    $url = $redirect;
                }

                $response = array(
                    'status'=> true,
                    'classe'=> 'success',
                    'message' => 'Login efetuado com sucesso! Você está sendo redirecionado para a Home. Clique <a href="'.$url.'">aqui</a> caso não seja redirecionado.',
                    'redirectModule' => $url
                );
            } else {

                $cookie = empty($cookie) ? 1 : ($cookie+1);
                set_cookie('try_login', $cookie, 600);

                $response = array(
                    'status'=> false,
                    'classe'=> 'error',
                    'message' => 'Usuário ou senha inválidos'
                );
            }
        }else{
            $response = array(
                'status'=> false,
                'classe'=> 'error',
                'message' => 'Usuário bloqueado por excesso de tentativas!'
            );
        }

        echo json_encode($response);
    }

    public function logout()
    {
        if ($this->auth->logout()) {
            redirect('login', 'location');
        }
    }
}
