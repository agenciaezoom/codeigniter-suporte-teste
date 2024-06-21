<?php (defined('BASEPATH')) or exit('No direct script access allowed');

class Usuarios extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->model('administracao_m');
    $this->load->model('grupos_m');
    $this->load->model('usuarios_m');

    // Mantem sessão flashdata
    $this->session->keep_filter(array('administracao/usuarios'));
  }

  public function index($pg = 1)
  {
    // Set Filtro
    $this->session->register_filter('administracao/usuarios', 'administracao/usuarios');
    $filter = ($this->session->flashdata('filter')) ? $this->session->flashdata('filter') : FALSE;
    $this->load->library('pagination');

    $search = $filter ? $filter['search'] : false;
    $total = $this->usuarios_m->get_users_total($search);
    $max = ($filter) ? (isset($filter['show']) ? $filter['show'] : 10) : 10;
    $start = ($pg - 1) * $max;

    $pagination = $this->pagination->init(array(
      'url' => site_url('administracao/usuarios'),
      'total' => $total,
      'max' => $max,
      'segment' => 4 // Segment no qual o numero da pagina estará
    ));

    $this->template
      ->add_css('css/usuarios')
      ->add_js('js/usuarios')
      ->set('title', SITE_NAME . ' - ' . T_('Usuários'))
      ->set('breadcrumb_route', array('administracao' => T_('Administração'), T_('Usuários')))
      ->set('users', $this->usuarios_m->get_users($search, $max, $start))
      ->set('paginacao', $pagination)
      ->set('search', $search)
      ->set('show', $max)
      ->set('total', $total)
      ->build('usuarios/usuarios');
  }

  public function cadastrar()
  {
    $this->_cadastrar_editar();
  }

  public function editar($id)
  {

    $id || show_404();

    $this->_cadastrar_editar($id);
  }

  public function _cadastrar_editar($id = false)
  {
    $user = null;
    $this->fileupload();
    $this->load->model('administracao/empresas_m');
    $companies = $this->empresas_m->get_all(array('where' => array('active_site' => 1, 'status' => 1)));

    if ($id) {
      $user = $this->usuarios_m->get(array('id' => $id));
      $user->permissions = json_decode($user->permissions, true);
      $user || show_404();

      $this->template
        ->set('title', SITE_NAME . ' - ' . T_('Editar Usuários'))
        ->set('breadcrumb_route', array('administracao' => T_('Administração'), 'administracao/controle-de-usuarios' => T_('Controle de Usuários'),  'administracao/usuarios' => T_('Usuários'), T_('Editar')))
        ->set('id', $id)
        ->set('existing_permissions', $user->permissions);
    } else {
      $this->template
        ->set('title', SITE_NAME . ' - ' . T_('Cadastrar Usuários'))
        ->set('breadcrumb_route', array('administracao' => T_('Administração'), 'administracao/controle-de-usuarios' => T_('Controle de Usuários'),  'administracao/usuarios' => T_('Usuários'), T_('Cadastrar')));
    }

    $this->template
      ->add_css('css/usuarios')
      ->add_js('js/usuarios')
      ->set('groups', $this->grupos_m->get_groups(false, 1000, 0))
      ->set('modules', $this->administracao_m->get_modules_type())
      ->set('companies', $companies)
      ->set('user', $user)
      ->build('usuarios/formulario');
  }

  public function add()
  {
    $this->load->library('form_validation');
    $this->form_validation->set_rules('name', T_('Nome'), 'trim|required');
    $this->form_validation->set_rules('login', T_('Usuário'), 'trim|required');
    $this->form_validation->set_rules('email', T_('E-mail'), 'trim|required|valid_email');
    $this->form_validation->set_rules('password', T_('Senha'), 'trim|required|matches[password2]');
    $this->form_validation->set_rules('password2', T_('Repetir Senha'), 'trim|required');
    $this->form_validation->set_rules('id_group', T_('Grupo'), 'trim|required');

    $this->form_validation->set_message('password', T_('Os campos de senhas devem ser preenchidos e devem ser iguais.'));
    $this->form_validation->set_message('password2', T_('Os campos de senhas devem ser preenchidos e devem ser iguais.'));

    if ($this->form_validation->run() === true) {

      $userExists = $this->usuarios_m->get(array('login' => $this->input->post('login')));
      $emailExists = $this->usuarios_m->get(array('email' => $this->input->post('email')));

      if ($userExists)
        $response = array('status' => FALSE, 'classe' => 'error', 'message' => T_('Este nome de usuário está sendo utilizado por outro usuário.'), 'redirect' => FALSE);
      else if ($emailExists)
        $response = array('status' => FALSE, 'classe' => 'error', 'message' => T_('Este e-mail já está sendo utilizado por outro usuário.'), 'redirect' => FALSE);
      else {

        $this->usuarios_m->insert($this->input->post());
        $response = array('status' => TRUE, 'classe' => 'success', 'message' => T_('Registro inserido com sucesso!'), 'redirect' => TRUE, 'redirectModule' => 'administracao/usuarios');
      }
    } else {
      $errors = array_values($this->form_validation->error_array());
      $response = array('status' => FALSE, 'classe' => 'error', 'message' => $errors[0], 'redirect' => FALSE);
    }

    if (!$response['status'])
      $this->fallback();

    echo json_encode($response);
  }

  public function edit($id)
  {
    $this->load->library('form_validation');
    $this->form_validation->set_rules('name', T_('Nome'), 'trim|required');
    $this->form_validation->set_rules('login', T_('Usuário'), 'trim|required');
    $this->form_validation->set_rules('email', T_('E-mail'), 'trim|required|valid_email');
    if ($this->auth->data('id') != $id)
      $this->form_validation->set_rules('id_group', T_('Grupo'), 'trim|required');

    if ($this->form_validation->run() === true) {

      $userExists = $this->usuarios_m->get(array(
        'login' => $this->input->post('login'),
        'id !=' => $this->input->post('id')
      ));
      $emailExists = $this->usuarios_m->get(array(
        'email' => $this->input->post('email'),
        'id !=' => $this->input->post('id')
      ));

      if ($userExists)
        $response = array('status' => FALSE, 'classe' => 'error', 'message' => T_('Este nome de usuário está sendo utilizado por outro usuário.'), 'redirect' => FALSE);
      else if ($emailExists)
        $response = array('status' => FALSE, 'classe' => 'error', 'message' => T_('Este e-mail já está sendo utilizado por outro usuário.'), 'redirect' => FALSE);
      else {

        $this->usuarios_m->update($this->input->post());
        $response = array('status' => TRUE, 'classe' => 'success', 'message' => T_('Registro editado com sucesso!'), 'redirect' => TRUE, 'redirectModule' => 'administracao/usuarios');
      }
    } else {
      $errors = array_values($this->form_validation->error_array());
      $response = array('status' => FALSE, 'classe' => 'error', 'message' => $errors[0], 'redirect' => FALSE);
    }

    if (!$response['status'])
      $this->fallback();

    echo json_encode($response);
  }

  public function modal($id = false)
  {
    $id || show_404();

    if ($this->auth->data('id') != $id && !$this->auth->data('admin'))
      show_404();

    $this->load->view('administracao/usuarios/modal', array('id' => $id));
  }

  public function change_password($id)
  {
    $this->load->library('form_validation');

    $this->form_validation->set_rules('password', T_('Senha'), 'trim|required|matches[password2]|min_length[6]');
    $this->form_validation->set_rules('password2', T_('Repetir Senha'), 'trim|required|min_length[6]');

    $this->form_validation->set_message('password', T_('Os campos de senhas devem ser preenchidos e devem ser iguais.'));
    $this->form_validation->set_message('password2', T_('Os campos de senhas devem ser preenchidos e devem ser iguais.'));
    $this->form_validation->set_message('min_length', '%s: O campo deve possuir no minímo %s caracteres');

    if ($this->form_validation->run() === true) {
      $this->usuarios_m->update_user_password($id, $this->input->post('password'));
      $response = array('status' => TRUE, 'classe' => 'success', 'message' => T_('Senha alterada com sucesso!'), 'redirect' => FALSE);
    } else {
      $errors = array_values($this->form_validation->error_array());
      $response = array('status' => FALSE, 'classe' => 'error', 'message' => $errors[0], 'redirect' => FALSE);
    }

    echo json_encode($response);
  }

  public function load_permissions()
  {
    $group = $this->grupos_m->get_group($this->input->post('id'));

    echo $this->load->view('usuarios/permissoes', array(
      'modules' => $this->administracao_m->get_modules_type(),
      'existing_permissions' => json_decode($group->permissions, true)
    ), TRUE);
  }

  public function fallback($data = array())
  {
    $this->load->helper('file');

    if (!empty($data['avatar'])) {
      $this->_delete_file($data['avatar']);
    }
  }
}
