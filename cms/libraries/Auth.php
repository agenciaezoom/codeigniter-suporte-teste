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
  private $ci;
  private $login_controller = 'login';
  private $dashboard_controller = 'home';

  private $auth = '';
  private $session_var = 'user_data';

  protected $currentModule;
  protected $sessionPermissions;
  protected $sessionModules;
  protected $module;
  protected $method;
  protected $class;

  //Classes que não devem validar login
  private $freepass = array(
    'login',
    'esqueci',
    'images',
    'cron'
  );

  //Classes que não usam o ez_company
  private $hide_company_switch = array(
    'atributos',
    'produtos',
    'fichas-tecnicas'
  );

  public function __construct()
  {
    //Registra no log a inicialização da library
    log_message('debug', 'Initial Auth Library');

    $this->ci = &get_instance();
    $this->ci->load->helper('url');

    //Recupera da sessão os dados do usuário
    $this->auth = $this->ci->session->userdata($this->session_var);
    //Pega informações da área acessada
    $this->module = $this->ci->router->fetch_module();
    $this->class = $this->ci->router->fetch_class();
    $this->method = $this->ci->router->fetch_method();

    //Verifica se deve executar a validação de login
    if (in_array($this->class, $this->freepass)) {
      return;
    } elseif (empty($this->auth)) {
      /**
       * Verifica se a requisição não é ajax
       * X-Requested-With: XMLHttpRequest
       */
      if (!$this->ci->input->is_ajax_request()) {
        //Pega página atual
        $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $this->ci->session->set_userdata(array('redirect' => $actual_link));

        redirect($this->login_controller);
      } else {
        set_status_header(401);
      }
    }

    /**
     * Verifica se a requisição não é ajax
     * X-Requested-With: XMLHttpRequest
     */
    if (!$this->ci->input->is_ajax_request()) {
      $this->refresh_data();
    }
    $this->sessionModules = $this->prepare_modules($this->data('permissions'));
    $this->check_current_module();
  }

  /**
   * Atualiza os dados de sessão
   * @author Fabio Neis [fabio@ezoom.com.br]
   * @date   2015-06-15
   * @return [boolean]
   */
  public function refresh_data()
  {
    $res = false;

    $this->ci->db->select('ez_user.id')
      ->select('ez_user.name')
      ->select('ez_user.companies')
      ->select('ez_user.admin')
      ->select('ez_user_group.name AS groupName')
      ->select('ez_user_group.id AS groupId')
      ->select('ez_user.id_architect')
      ->select('
                        (CASE
                            WHEN ez_file.file IS NULL THEN IF(ez_user.avatar IS NOT NULL,
                                CONCAT("userfiles/avatar/", ez_user.avatar),
                                ""
                            )
                            ELSE IF(ez_file.file IS NOT NULL,
                                CONCAT("userfiles/avatar/", ez_file.file),
                                ""
                            )
                        END) as avatar
                     ', false)
      ->from('ez_user')
      ->join('ez_user_group', 'ez_user_group.id = ez_user.id_group', 'INNER')
      ->join('ez_file', 'ez_file.id = ez_user.avatar', 'left')
      ->where('ez_user.deleted', null)
      ->where('ez_user.status', 1)
      ->where('ez_user.id', $this->data('id'));
    $query = $this->ci->db->get();
    $user = $query->row();

    if ($user) {
      $currentCompany = explode(',', $user->companies);
      $currentCompany = isset($this->auth->company) ? $this->auth->company : array_shift($currentCompany);

      $this->auth = $user;
      $this->auth->id_company = $currentCompany;
      $this->auth->company = $currentCompany;
      $this->auth->companies = $user->companies;
      $this->auth->permissions = $this->get_permissions();
      $this->auth->language_main = $this->get_language();
      $this->ci->session->set_userdata(array($this->session_var => $this->auth));
      $res = true;
    }

    return $res;
  }

  public function get_language()
  {
    $res = false;

    $this->ci->db->select('ez_language.code')
      ->from('ez_language')
      ->join('ez_company', 'ez_company.id = ' . $this->auth->id_company . ' AND ez_company.language_main = ez_language.id', 'inner', FALSE);
    $query = $this->ci->db->get();
    return $query->row('code');
  }

  /**
   * Verifica permissao de acesso ao modulo.
   * @author Fábio Augustin Neis
   * @date   2015-06-15
   */
  public function check_current_module()
  {
    if (!in_array($this->module, $this->freepass)) {
      $currentSlug = '';
      if ($this->module == $this->class)
        $currentSlug = $this->module;
      else
        $currentSlug = $this->module . '/' . $this->class;

      $alternative = $currentSlug . '/' . $this->method;

      $this->ci->load->model('comum/comum_m');
      $this->currentModule = $this->ci->comum_m->get_current_module($currentSlug, $alternative);

      $this->verify_allowed_action($this->currentModule);
    }
  }

  /**
   * Verifica se o usuário tem permissão para acessar determinada página
   * @author Fabio Neis [fabio@ezoom.com.br]
   * @date   2015-06-15
   * @param  [type]          $currentModule Dados do módulo a ser acessado
   * @return [redirect|exit]
   */
  public function verify_allowed_action($currentModule)
  {
    $deny = false;

    if ($currentModule) {
      if (isset($this->sessionPermissions[$currentModule->id])) {
        $method = $this->method;
        if (($method == 'cadastrar' || $method == 'add') && !in_array('cadastrar', $this->sessionPermissions[$currentModule->id])) {
          $deny = true;
        } elseif (($method == 'editar' || $method == 'edit') && !in_array('editar', $this->sessionPermissions[$currentModule->id])) {
          $deny = true;
        } elseif (($method == 'delete') && !in_array('excluir', $this->sessionPermissions[$currentModule->id])) {
          $deny = true;
        }
      } else {
        $deny = true;
      }
    }

    if ($deny) {
      $this->deny_access('Você não tem permissão para realizar esta operação.');
    }
  }

  /**
   * Controla o redirecionamento para quando o usuário não tiver alguma permissão
   * @author Fabio Neis [fabio@ezoom.com.br]
   * @date   2015-06-16
   * @param  string          $message
   * @param  string          $redirect
   * @return [redirect|json]
   */
  public function deny_access($message = 'Você não tem permissão para acessar este módulo.', $redirect = 'home')
  {
    if (!$this->ci->input->is_ajax_request()) {
      $this->ci->session->set_flashdata('message', array(
        'status' => false,
        'classe' => 'error',
        'message' => $message
      ));
      redirect($redirect);
    } else {
      $data = array(
        'status' => false,
        'classe' => 'error',
        'message' => $message,
        'redirect' => site_url($redirect)
      );
      $this->ci->output
        ->set_content_type('application/json')
        ->set_output(json_encode($data));
      die();
    }
  }

  /**
   * Tenta fazer o login do usuário
   * @author Fabio Neis [fabio@ezoom.com.br]
   * @date   2015-06-12
   * @param  [string]  $user
   * @param  [string]  $pass
   * @return [boolean]
   */
  public function login($user, $pass)
  {
    $this->ci->load->library('PasswordHash');
    $res = false;

    $this->ci->db->select('ez_user.id')
      ->select('ez_user.name')
      ->select('ez_user.password')
      ->select('ez_user_group.name AS groupName')
      ->select('
                        (CASE
                            WHEN ez_file.file IS NULL THEN IF(ez_user.avatar IS NOT NULL,
                                CONCAT("userfiles/avatar/", ez_user.avatar),
                                ""
                            )
                            ELSE IF(ez_file.file IS NOT NULL,
                                CONCAT("userfiles/avatar/", ez_file.file),
                                ""
                            )
                        END) as avatar
                     ', false)
      ->from('ez_user')
      ->join('ez_user_group', 'ez_user_group.id = ez_user.id_group', 'INNER')
      ->join('ez_file', 'ez_file.id = ez_user.avatar', 'left')
      ->where('ez_user.status', 1)
      ->where('ez_user.deleted', null)
      ->where('(
                        ez_user.login = ' . $this->ci->db->escape($user) . ' OR
                        ez_user.email = ' . $this->ci->db->escape($user) . ')');
    $query = $this->ci->db->get();
    $user = $query->row();

    if ($user && $this->ci->passwordhash->CheckPassword($pass, $user->password)) {
      //Remove a senha para não salvar em sessão
      unset($user->password);
      $this->auth = $user;
      $this->ci->session->set_userdata(array($this->session_var => $this->auth));

      $this->ci->db->where('id', $user->id);
      $this->ci->db->update('ez_user', array('last_access' => date('Y-m-d H:i:s'), 'online' => 1));

      if (!isset($_SESSION['ezoom_cms']))
        $_SESSION['ezoom_cms'] = array();
      $_SESSION['ezoom_cms'][$_SERVER['HTTP_HOST']] = TRUE;

      $res = true;
    }

    return $res;
  }

  /**
   * Retorna os dados do usuário logado
   * @author Fabio Neis [fabio@ezoom.com.br]
   * @date   2015-06-12
   * @return [object]
   */
  public function logged()
  {
    return $this->auth;
  }

  /**
   * Desloga o usuário
   * @author Fabio Neis [fabio@ezoom.com.br]
   * @date   2015-06-12
   * @return [boolean]
   */
  public function logout()
  {
    $this->ci->db->where('id', $this->data('id'));
    $this->ci->db->update('ez_user', array('online' => 0));

    $this->ci->session->set_userdata($this->session_var, '');
    $this->auth = '';

    unset($_SESSION['ezoom_cms'][$_SERVER['HTTP_HOST']]);

    return true;
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

  /**
   * Busca as permissões do usuário logado
   * @author Fabio Neis [fabio@ezoom.com.br]
   * @date   2015-06-15
   * @return [json]
   */
  private function get_permissions()
  {
    $this->ci->db->select('ez_user.*')
      ->select('ez_user_group.name AS groupName')
      ->select('ez_user_group.permissions as groupPermissions')
      ->select('ez_user_group.flags as groupFlags')
      ->from('ez_user')
      ->join('ez_user_group', 'ez_user_group.id = ez_user.id_group', 'INNER')
      ->where('ez_user.deleted', null)
      ->where('ez_user.id', $this->data('id'));
    $query = $this->ci->db->get();
    $user = $query->row();

    $permissions = ($user) ? (($user->permissions) ? $user->permissions : $user->groupPermissions) : false;

    return json_decode($permissions, true);
  }

  /**
   * Retorna a relação de módulos que não precisam validar login
   * @author Fabio Neis [fabio@ezoom.com.br]
   * @date   2015-06-15
   * @return [array]
   */
  public function get_freepass()
  {
    return $this->freepass;
  }

  /**
   * Retorna os dados do módulo acessado
   * @author Fabio Neis [fabio@ezoom.com.br]
   * @date   2015-06-16
   * @return [object]
   */
  public function get_current_module()
  {
    return $this->currentModule;
  }

  /**
   * Retorna a permissão do usuário para o módulo acessado
   * @author Fabio Neis [fabio@ezoom.com.br]
   * @date   2015-06-16
   * @return [type] [description]
   */
  public function get_session_permissions()
  {
    return $this->sessionPermissions;
  }

  /**
   * Montagem do HTML do menu.
   * @author Fabio Bachi [fabio.bachi@ezoom.com.br]
   * @date   2014-08-22
   * @param  string $currentUri URL atual da pagina sem a primeira barra.
   * @param  string $modules    Lista dos módulos a percorrer.
   * @return string HTML do menu construido.
   *
   * @changes Fábio Augustin Neis
   * @date    15-06-2015
   * Alterado ordem dos parâmetros e adicionado $prepare_module
   */
  public function create_menu($currentUri, $modules = false, $prepare_module = false)
  {
    if ($this->logged() == '') {
      return;
    }

    if ($prepare_module) {
      $modules = $this->sessionModules;
    }

    $fixed_modules = array();
    $return = '';
    if (!empty($modules)) {
      $listPermissions = $this->get_permissions_by_user($this->auth->permissions);

      foreach ($modules as $key => $module) {
        if ($module->visible != '1') {
          continue;
        }
        $fixed_modules[$key] = $module;
      }
      $modules = $fixed_modules;

      foreach ($modules as $key => $module) {
        if (isset($module->children)) {
          $menu = $this->create_menu($currentUri, $module->children);
          if (empty($menu)) {
            continue;
          }
          $return .= $this->ci->load->view('comum/menu-parent', array(
            'module' => $module,
            'children' => $menu
          ), true);
        } else {
          if (!empty($listPermissions) && in_array('visualizar', $listPermissions[$key])) {
            $return .= $this->ci->load->view('comum/menu-item', array(
              'module' => $module,
              'currentUri' => $currentUri
            ), true);
          }
        }
      }
    }

    return $return;
  }

  /**
   * Busca as informacoes de cada modulo permitido para o usuario.
   * @author Fabio Bachi [fabio.bachi@ezoom.com.br]
   * @date   2014-08-22
   * @param  array $modules Array de modulos.
   * @return array Array de modulos com as informacoes.
   */
  public function prepare_modules($modules)
  {
    $return = array();
    $system = pathinfo(FCPATH, PATHINFO_BASENAME);
    if ($system != 'cms' && strstr($system, 'cms'))
      $system = 'cms';

    foreach ($modules as $key => $module) {
      $this->ci->db->select('ez_module.*, ez_module_company.*, ez_module_description.name, ez_module_description.slug')
        ->from('ez_module')
        ->join('ez_module_description', 'ez_module.id = ez_module_description.id_module', 'left')
        ->join('ez_language', 'ez_language.id = ez_module_description.id_language')
        ->join('ez_module_company', 'ez_module.id = ez_module_company.id_module')
        ->where(array(
          //'system' => pathinfo(FCPATH, PATHINFO_BASENAME),
          'ez_module.id' => $key,
          'ez_module.status' => '1',
        ))
        // ->where("FIND_IN_SET('" . $this->data('company') . "', ez_module_company.id_module) !=", 0)
        ->where("ez_module_company.id_company = '" . $this->data('company') . "'")
        ->order_by('ez_module_description.id_language', 'DESC')
        ->order_by('ez_module.order_by');

      if (isset($_SESSION['user_lang']) && !empty($_SESSION['user_lang']))
        $this->ci->db->where_in('ez_language.code', array($_SESSION['user_lang'], 'pt'));
      else
        $this->ci->db->where('ez_language.id', 1);

      $query = $this->ci->db->get();
      $data = $query->row();

      if ($data && $data->system == $system) {
        $return[$key] = $data;
        if (is_array($module) && !isset($module[0])) {
          $return[$key]->children = $this->prepare_modules($module);
        } else {
          $this->sessionPermissions[$key] = $module;
        }
      }
    }
    return $return;
  }

  /**
   * Verifica se deve exibir o botão de empresa em determinado módulo
   * @author Fabio Neis [fabio@ezoom.com.br]
   * @date   2015-06-15
   * @return [boolean]
   */
  public function show_company_switch()
  {
    return (!in_array($this->module, $this->hide_company_switch));
  }

  /**
   * Adiciona na sessão do usuário alguma informação extra
   * @author Fabio Neis [fabio@ezoom.com.br]
   * @date   2015-06-16
   * @param [type] $values [description]
   */
  public function set_userdata($values)
  {
    $res = false;

    if (is_array($values)) {
      foreach ($values as $key => $value) {
        $this->auth->{$key} = $value;
      }
      $this->ci->session->set_userdata($this->session_var, $this->auth);
      $res = true;
    }

    return $res;
  }

  /**
   * Retorna o html das permissoes para os modulos.
   * @author Fabio Bachi [fabio.bachi@ezoom.com.br]
   * @date   2014-08-18
   * @param  [array]  $modules [Array com os modulos a percorrer]
   * @param  [int]    $colSize [Tamanho da coluna no HTML]
   * @param  integer  $depth   [Nivel percorrido no momento]
   * @param  string   $parent  [Name dos proximos checkbox]
   * @return [string] [Retorna o HTML completo dos modulos e suas permissoes.]
   */
  public function build_permissions($modules, $colSize, $depth = 0, $parent = '', $permissions = FALSE)
  {
    $ci = &get_instance();
    $print = '';
    $permissions = $permissions ? $permissions : $this->auth->permissions;

    foreach ($modules as $key => $module) {
      $mod = '';

      $print .= '<div class="col-sm-12">';
      if (count($module->children) > 0) {
        $mod .= $this->build_permissions($module->children, 6, $depth + 1, $parent . '[' . $module->id . ']', (isset($permissions[$module->id]) ? $permissions[$module->id] : FALSE));
      } else {
        if ($this->auth->admin == '1' || isset($permissions[$module->id])) {
          $mod .= $ci->load->view('administracao/permissoes/permissoes', array(
            'module' => $module,
            'colSize' => $colSize,
            'depth' => $depth,
            'parentsName' => $parent,
            'permissions' => isset($permissions[$module->id]) ? $permissions[$module->id] : array_map(function ($el) {
              return strtolower($el);
            }, explode(',', $module->action))
          ), true);
        }
      }
      if ($this->auth->admin == '1' || isset($permissions[$module->id])) {
        $print .= $ci->load->view('administracao/permissoes/permissoes-cont', array(
          'modules' => $mod,
          'parent' => $module,
          'colSize' => $colSize,
          'permissions' => isset($permissions[$module->id]) ? $permissions[$module->id] : array_map(function ($el) {
            return strtolower($el);
          }, explode(',', $module->action))
        ), true);
      }

      $print .= '</div>';
    }

    return $print;
  }

  /**
   * Retorna a key e os valores das permissões do modulo
   * @author Diogo Taparello e Michael Cruz [diogo@ezoom.com.br/michael@ezoom.com.br]
   * @date   2014-08-18
   * @param  [array]    $array [array de permissões do usuário]
   * @return [array] [key and values]
   */
  private function get_permissions_by_user($permissions)
  {
    $list_permissions = array();
    foreach ($permissions as $key => $value) {
      if (isset($value[0]))
        $list_permissions[$key] = $value;
      else
        $list_permissions = $list_permissions + $this->get_permissions_by_user($value);
    }
    return $list_permissions;
  }
}
