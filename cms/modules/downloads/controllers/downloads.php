<?php (defined('BASEPATH')) or exit('No direct script access allowed');

class Downloads extends MY_Controller
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

  protected function formulario($id = false)
  {
    $this->fileupload();

    $this->load->model('produtos/produtos_m');
    $products = $this->produtos_m->get(array('where' => array('status' => 1)));

    $this->template
      ->add_css('css/gallery', 'gallery')
      ->add_js('js/gallery', 'gallery')
      ->set('products', $products);

    parent::formulario($id);
  }

  public function add()
  {
    $this->load->library('form_validation');

    foreach ($this->languages as $key => $language) {
      if ($language->code == 'pt') {
        $this->form_validation->set_rules('file[archive][' . $language->id . ']', T_('Arquivo') . ' (' . $language->code . ')', 'trim|required');
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

  public function edit($id)
  {
    $this->load->library('form_validation');
    $this->form_validation->set_rules('id', 'ID', 'trim|required');

    $image = $this->input->post();
    if (!isset($image) || $image == '' || isset($image['delete-file']['archive'][1])) {
      $this->form_validation->set_rules('file[image]', T_('Arquivo'), 'trim|required');
    }

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
