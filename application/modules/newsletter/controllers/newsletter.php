<?php (defined('BASEPATH')) or exit('No direct script access allowed');

class Newsletter extends MY_Controller
{
  public function index()
  {
    return $this->load->view('newsletter', array(), TRUE);
  }

  public function send()
  {
    if (!$this->input->is_ajax_request())
      show_404();

    // Validação
    $this->load->library('form_validation');
    $this->form_validation->set_rules('name', T_('Nome'), 'required');
    $this->form_validation->set_rules('email', T_('E-mail'), 'required|valid_email|is_unique[site_newsletter.email]');
    $this->form_validation->set_message('is_unique', T_('Este e-mail já está cadastrado!'));

    if ($this->form_validation->run()) {

      $this->load->helper('email_helper');

      $sendTo = ENVIRONMENT == 'development' ? 'ricardo.campeol@ezoom.com.br' : $this->company->email;

      $data = $this->input->post();
      $data['id_company'] = $this->auth->data('company');
      unset($data['csrf_test_name']);

      $emails = array(
        'to' => array($sendTo),
        'replyTo' => $data['email']
      );

      $body = array(
        'Nome' => mb_convert_encoding($data['name'], "utf-8", "auto"),
        'E-mail' => mb_convert_encoding($data['email'], "utf-8", "auto")
      );

      if ($this->db->insert('site_newsletter', $data)) {
        enviar_email($emails, 'Tecnomaq - Newsletter', $body);
        $retorno = array('status' => TRUE, 'class' => 'success', 'title' => T_('Obrigado!'), 'message' => T_('Sua mensagem foi enviada com sucesso.'));
      } else
        $retorno = array('status' => FALSE, 'class' => 'warning', 'title' => T_('Oops!'), 'message' => T_('Erro ao cadastrar newsletter'), 'fail' => $this->form_validation->error_array(TRUE));
    } else {
      $retorno = array('status' => FALSE, 'class' => 'warning', 'title' => T_('Oops!'), 'message' => strip_tags(validation_errors()), 'fail' => $this->form_validation->error_array(TRUE));
    }

    $this->output->set_content_type('application/json')
      ->set_output(json_encode($retorno));
  }
}
