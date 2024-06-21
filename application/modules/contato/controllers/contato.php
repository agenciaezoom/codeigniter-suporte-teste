<?php (defined('BASEPATH')) or exit('No direct script access allowed');

class Contato extends MY_Controller
{
  public function __construct($isRun = FALSE)
  {
    parent::__construct();
  }

  public function index()
  {
    if (empty($this->currentDbRoute))
      $this->template->set('title', $this->company->meta_title);

    $states = $this->comum_m->get_states();
    $contents = $this->comum_m->getPageContent(array('area' => 'Contato'));

    $this->load->model(PATH_TO_MODEL . 'setores/models/setores_m', 'setores_m');
    $departments = $this->setores_m->get(['where' => ['status' => 1]]);

    $this->template
      ->add_js('plugins/masks.min.js', 'comum')
      ->add_css('css/contato')
      ->add_js('js/contato')
      ->set('contents', $contents)
      ->set('states', $states)
      ->set('departments', $departments)
      ->build('contato');
  }

  public function send()
  {
    if (!$this->input->is_ajax_request())
      show_404();

    // Validação
    $this->load->library('form_validation');
    $this->form_validation->set_rules('name', T_('Nome'), 'required');
    $this->form_validation->set_rules('phone', T_('Telefone'), 'required');
    $this->form_validation->set_rules('email', T_('Email'), 'required|valid_email');
    $this->form_validation->set_rules('id_department', T_('Setor de Interesse'), 'required');
    $this->form_validation->set_rules('message', T_('Mensagem'), 'required');

    if ($this->form_validation->run()) {

      $this->load->helper('email_helper');
      $this->load->model(PATH_TO_MODEL . 'contato/models/contato_m', 'contato_m');

      $data = $this->input->post();
      $id = $this->contato_m->insert($data);

      $this->load->model(PATH_TO_MODEL . 'setores/models/setores_m', 'setores_m');
      $department = $this->setores_m->get(['id' => $data['id_department']]);
      if ($department && !empty($department->emails)) {
        $emailTo = explode(';', $department->emails);
      } else {
        $emailTo = $this->company_email;
      }

      $emails = array(
        'to' => ENVIRONMENT == 'development' ? 'teste@ezoom.com.br' : $emailTo,
        'replyTo' => $data['email']
      );

      $body = array(
        'Nome' => mb_convert_encoding($data['name'], "utf-8", "auto"),
        'Telefone' => mb_convert_encoding(rtrim($data['phone'], '_'), "utf-8", "auto"),
        'E-mail' => mb_convert_encoding($data['email'], "utf-8", "auto"),
        'Setor de Interesse' => mb_convert_encoding($department->title ?? 'Setor desconhecido', "utf-8", "auto"),
        'Mensagem' => mb_convert_encoding($data['message'], "utf-8", "auto")
      );

      $retorno = array('status' => TRUE, 'class' => 'success', 'title' => T_('Obrigado!'), 'message' => T_('Sua mensagem foi enviada com sucesso!'));

      if ($id) {
        if (enviar_email($emails, 'Soulx - Contato', $body)) {
          $this->contato_m->update_contact($id, array('status' => 'Não lido'));
        } else {
          $this->contato_m->update_contact($id, array('status' => 'Não enviado'));
        }
      }
    } else {
      $retorno = array('status' => FALSE, 'class' => 'warning', 'title' => T_('Ocorreu um erro!'), 'message' => strip_tags(validation_errors()), 'fail' => $this->form_validation->error_array(TRUE));
    }

    $this->output
      ->set_content_type('application/json')
      ->set_output(json_encode($retorno));
  }
}
