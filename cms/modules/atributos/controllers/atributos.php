<?php (defined('BASEPATH')) or exit('No direct script access allowed');

class Atributos extends MY_Controller
{
  public function __construct()
  {
    parent::__construct();
  }

  public function index($pg = 1)
  {
    parent::index($pg);
  }

  public function cadastrar()
  {
    $this->formulario();
  }

  public function editar($id)
  {
    parent::editar($id);
    $this->formulario($id);
  }

  protected function formulario($id = false, $build = FALSE)
  {
    $this->fileupload();
    parent::formulario($id);
  }

  public function add()
  {
    $this->load->library('form_validation');

    $this->form_validation->set_rules('link', T_('Link'), 'valid_url|trim');
    $this->form_validation->set_rules('link_label', T_('Texto do Link'), 'trim');

    foreach ($this->languages as $key => $language) {
      if ($language->code == 'pt') {
        $this->form_validation->set_rules('value[' . $language->id . '][title]', T_('Título') . ' (' . $language->code . ')', 'trim|required');
      }
    }
    if ($this->form_validation->run() === TRUE) {
      parent::add();
    } else {
      $errors = array_values($this->form_validation->error_array());
      $response = array('status' => false, 'classe' => 'error', 'message' => $errors[0], 'redirect' => false);
      $this->output->set_output(json_encode($response));
    }
  }

  public function edit($id = null)
  {
    $this->load->library('form_validation');
    $this->form_validation->set_rules('id', 'ID', 'trim|required');
    $this->form_validation->set_rules('link', T_('Link'), 'valid_url|trim');
    $this->form_validation->set_rules('link_label', T_('Texto do Link'), 'trim');

    foreach ($this->languages as $key => $language) {
      if ($language->code == 'pt') {
        $this->form_validation->set_rules('value[' . $language->id . '][title]', T_('Título') . ' (' . $language->code . ')', 'trim|required');
      }
    }
    if ($this->form_validation->run() === TRUE) {
      parent::edit($id);
    } else {
      $errors = array_values($this->form_validation->error_array());
      $response = array('status' => false, 'classe' => 'error', 'message' => $errors[0], 'redirect' => false);
      $this->output->set_output(json_encode($response));
    }
  }
}
