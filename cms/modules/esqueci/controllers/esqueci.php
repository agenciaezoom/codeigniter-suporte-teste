<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Esqueci extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('esqueci_m');
    }

    public function index()
    {
        $this->template->set_template('login');
        $this->template
             ->add_css('css/esqueci')
             ->add_js('js/esqueci')
             ->set('title', SITE_NAME.' - Esqueci minha senha')
             ->set_partial('header', '')
             ->set_partial('sidebar', '')
             ->set_partial('breadcrumb', '')
             ->set_partial('footer', '')
             ->build('esqueci');
    }

    public function request_token()
    {
        // Validação
        $this->load->library('form_validation');

        $this->form_validation->set_rules('username', 'E-mail/Usuário', 'required|trim');
        $return = array(
            'status'=> FALSE,
            'classe'=> 'error',
            'message' => 'Ocorreu um erro ao tentar resetar sua senha. Tente novamente mais tarde.'
        );
        if ($this->form_validation->run()) {
            $this->load->helper('email_helper');
            $user = $this->esqueci_m->get_email($this->input->post());
            if(!empty($user)) {
                $nova_senha = $this->esqueci_m->set_new_password($user->id);
                $emails = array(
                    'to' => $user->email
                );

                $body = array('Sua nova senha' => $nova_senha);

                if(enviar_email($emails, "Nova senha", $body, true)) {
                    $return = array(
                        'status'=> TRUE,
                        'classe'=> 'success',
                        'message' => 'Um e-mail foi enviado com sucesso para '.$user->email
                    );
                }
            } else {
                $return = array(
                    'status'=> FALSE,
                    'classe'=> 'error',
                    'message' => 'Este e-mail não está cadastrado.'
                );
            }
        }
        echo json_encode($return);
    }

    public function check_token($token)
    {
        $token || show_404();
        $this->template->set_template('login');

        $this->template
             ->add_css('css/esqueci')
             ->add_js('js/esqueci')
             ->set('hash', $token)
             ->set('title', SITE_NAME.' - Esqueci minha senha')
             ->set_partial('header', '')
             ->set_partial('sidebar', '')
             ->set_partial('breadcrumb', '')
             ->set_partial('footer', '')
             ->build('esqueci');
    }

    public function change_password()
    {
        // Validação
        $this->load->library('form_validation');

        $this->form_validation->set_rules('password', 'Nova Senha', 'required|matches[password2]|trim|min_length[6]');
        $this->form_validation->set_rules('password2', 'E-mail', 'required|trim|min_length[6]');
        $this->form_validation->set_rules('token', 'Destinatário', 'required|trim'); // |is_unique[forgot.token]

        if ($this->form_validation->run()) {
            // Changeia senha
            $return = array('status'=> TRUE, 'classe'=> 'success','message' => 'Sua senha foi alterada com sucesso!');
        }else{
            $return = array('status'=> FALSE, 'classe'=> 'error','message' => validation_errors());
        }
        echo json_encode($return);
    }

}

/* End of file esqueci.php */
/* Location: ./application/controllers/esqueci.php */
