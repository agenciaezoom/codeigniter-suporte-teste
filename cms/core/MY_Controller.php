<?php (defined('BASEPATH')) or exit('No direct script access allowed');

/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package CodeIgniter
 * @author  ExpressionEngine Dev Team
 * @copyright  Copyright (c) 2006, EllisLab, Inc.
 * @license http://codeigniter.com/user_guide/license.html
 * @link http://codeigniter.com
 * @since   Version 2.1.4
 * @filesource
 */

// --------------------------------------------------------------------

/**
 * CodeIgniter MY_Controller
 *
 * Controller principal da aplicação.
 *
 * @package     CodeIgniter
 * @author      Ezoom
 * @subpackage  Controllers
 * @category    Controllers
 * @link        http://ezoom.com.br
 * @copyright  Copyright (c) 2008, Ezoom
 * @version 1.0.0
 *
 */
class MY_Controller extends MX_Controller
{

  protected $title;
  protected $filter;
  protected $module;
  protected $method;
  protected $class;
  protected $current_lang;
  protected $current_module;
  protected $view;
  protected $slug;
  protected $model;
  public $languages;
  public $all_companies;
  public $company;
  public $params = array();

  public function __construct()
  {
    parent::__construct();
    //Carrega o template
    $this->load->library('template');

    //Carrega bibliotecas quando em desenvolvimento
    if (ENVIRONMENT == 'development') {
      //$this->load->library('whoops');
      $this->load->helper('debug');
      if ($this->input->is_ajax_request()) {
        $this->output->enable_profiler(false);
      } else {
        $this->output->enable_profiler($this->config->item('enable_profiler'));
      }
      //CSS REFRESH
      $norefresh = $this->input->get('norefresh');
      if (
        !empty($norefresh) &&
        $_SERVER['HTTP_HOST'] == 'localhost' &&
        $this->config->item('cssrefresh') === true
      ) {
        $this->template->add_js('plugins/cssrefresh', 'comum');
      }
    }

    $this->title = SITE_NAME;

    $this->load->helper('language');
    $this->load->helper('url');

    $this->lang->load('default');
    $this->load->model('comum/comum_m');
    $this->load->model('administracao/empresas_m');

    $this->module = $this->router->fetch_module();
    $this->class = $this->router->fetch_class();
    $this->method = $this->router->fetch_method();
    $this->current_lang = $this->lang->lang();
    $this->current_module = $this->auth->get_current_module();
    $this->company = $this->empresas_m->get(array('id' => $this->auth->data('company')));
    $this->all_companies = ($this->current_module) ? $this->empresas_m->get_all() : null;
    $this->languages = $this->comum_m->get_languages($this->company);

    $this->slug = $this->module == $this->class
      ? str_replace('_', '-', $this->module)
      : str_replace('_', '-', $this->module . '/' . $this->class);

    //Carrega a pasta language de cada module, quando existe
    $this->lang->load($this->module . '/default');

    // Carregamento de plugins padrões
    $this->load_default();

    // Carregamento das configurações do modulo acessado
    $this->load_module();
  }

  /**
   * Carregamento das configurações principais do sistema
   * @author Ramon Barros [ramon@ezoom.com.br]
   * @date   2015-03-26
   * @return void
   */
  public function load_default()
  {
    /**
     * Verifica se a requisição não é ajax
     * X-Requested-With: XMLHttpRequest
     */
    if (!$this->input->is_ajax_request()) {

      // Mantem sessão flashdata
      if ($this->current_module)
        $this->session->keep_filter(array($this->current_module->slug));

      // andlog (Desativa o console.log em produção)
      $this->template
        ->add_js('plugins/andlog/andlog.min.js', 'comum');

      // Moment
      $this->template
        ->add_js('plugins/moment/moment.min', 'comum')
        ->add_js('plugins/moment/moment.pt-br', 'comum');

      $this->template
        //bootstrap
        ->add_css('plugins/bootstrap/css/bootstrap.min', 'comum')
        ->add_css('plugins/bootstrap/css/bootstrap-select.min', 'comum')
        ->add_css('plugins/bootstrap/css/bootstrap-datetimepicker.min', 'comum')
        ->add_js('plugins/bootstrap/js/bootstrap.min', 'comum')
        ->add_js('plugins/bootstrap/js/bootstrap-select.min', 'comum')
        // (Depende Momment)
        ->add_js('plugins/bootstrap/js/bootstrap-datetimepicker.min', 'comum')

        //magnific
        ->add_js('plugins/magnific/jquery.magnific-popup.min.js', 'comum')
        ->add_css('plugins/magnific/magnific-popup.css', 'comum')

        //Multi Select
        ->add_js('plugins/select2/js/select2.full', 'comum')
        ->add_css('plugins/select2/css/select2.min', 'comum')

        //notyfy
        ->add_css('plugins/notyfy/css/jquery.notyfy', 'comum')
        ->add_js('plugins/notyfy/js/jquery.notyfy.js', 'comum')

        //Checkbox
        ->add_css('plugins/customcheckbox/checkbox', 'comum')
        ->add_js('plugins/customcheckbox/checkbox', 'comum')

        //Alert bootbox
        ->add_js('plugins/bootbox.min', 'comum')

        //Validate
        ->add_js('plugins/validate/jquery.validate.min', 'comum')

        //Holder
        ->add_js('plugins/holder', 'comum')

        //Checkbox
        ->add_js('plugins/tables', 'comum')

        //Jquery UI
        ->add_css('plugins/jquery-ui/jquery-ui.min', 'comum')
        ->add_css('plugins/jquery-ui/jquery-ui.theme.min', 'comum')
        ->add_js('plugins/jquery-ui/jquery-ui.min', 'comum')

        // Switch
        ->add_css('plugins/switch/switch', 'comum')
        ->add_js('plugins/switch/switch', 'comum')

        //Scroll Bar
        ->add_js('plugins/mcustomscrollbar/jquery.mousewheel.min', 'comum')
        ->add_js('plugins/mcustomscrollbar/jquery.mcustomscrollbar.min', 'comum')
        ->add_css('plugins/mcustomscrollbar/jquery.mcustomscrollbar', 'comum')

        // Input Mask
        ->add_js('plugins/jquery.inputmask.bundle.min', 'comum')
        // Comum
        ->add_js('js/main.min', 'comum');

      $logo = 'userfiles' . DS . 'logo.png';
      if (!file_exists($logo)) {
        $logo = false;
      }

      $this->template
        ->set('lang', $this->current_lang)
        ->add_css('css/main', 'comum')
        ->add_js('plugins/plugins', 'comum')
        ->set('logo', $logo)
        ->set('title', 'Dashboard ' . (isset($this->company->title) ? '- ' . $this->company->title : ''))
        ->set_partial('header', 'header', 'comum')
        ->set_partial('sidebar', 'sidebar', 'comum')
        ->set_partial('breadcrumb', 'breadcrumb', 'comum')
        ->set_partial('footer', 'footer', 'comum')
        ->set('i18n', $this->_js_translation())
        ->set('module', $this->module)
        ->set('class', $this->class)
        ->set('method', $this->method)
        ->set('slug', $this->slug)
        ->set('order_by', $this->session->flashdata('order_by'))
        ->set('languages', $this->languages)
        ->set('all_companies', $this->all_companies)
        ->set('company', $this->company)
        ->set('current_module', $this->current_module)
        ->set('sidebar_menu', $this->auth->create_menu(substr($this->uri->uri_string(), 1), '', true))
        ->set('session_permissions', $this->auth->get_session_permissions());
    }
  }

  public function index($pg = 1, $pBuild = TRUE)
  {
    //Set Filtro
    $this->session->register_filter($this->current_module->slug, $this->current_module->slug);
    $filter = ($this->session->flashdata('filter')) ? $this->session->flashdata('filter') : false;
    $this->load->library('pagination');

    //Paginação considerando a busca
    $search = $filter ? $filter['search'] : false;
    $params = array('search' => $search, 'count' => true);

    if (isset($this->params))
      $params = array_merge($params, $this->params);

    $total = $this->model->get($params);

    $max = ($filter) ? (isset($filter['show']) ? $filter['show'] : 10) : 10;
    $start = ($pg - 1) * $max;

    $segment = explode('/', $this->current_module->slug);

    $pagination = $this->pagination->init(array(
      'url' => site_url($this->current_module->slug),
      'total' => $total,
      'max' => $max,
      'segment' => count($segment) + 2
    ));

    //Resultado da busca na listagem
    $showing = '';
    if ($total > 0) {
      $totalPage = (($start + $max) > $total) ? $total : ($start + $max);
      $showing = T_('Exibindo resultados ') . ($start + 1) . ' - ' . $totalPage . T_(' de um total de ') . $total;
    } else if ($search)
      $showing = T_('Não há resultados para esta busca.');

    //Busca no banco registros para a listagem
    $params = array(
      'search' => $search,
      'offset' => $start,
      'limit' => $max,
      'order_by' => $this->session->flashdata('order_by'),
    );

    if (isset($this->params))
      $params = array_merge($params, $this->params);

    $items = $this->model->get($params);

    $this->template
      ->set('title', SITE_NAME . ' - ' . $this->current_module->name)
      ->set('breadcrumb_route', array($this->current_module->name))
      ->set('items', $items)
      ->set('paginacao', $pagination)
      ->set('showing', $showing)
      ->set('search', $search)
      ->set('show', $max)
      ->set('total', $total);

    if ($pBuild)
      $this->template->build('listagem');
  }

  public function ckeditor()
  {
    $this->template
      ->add_js('plugins/ckeditor/ckeditor.js', 'comum')
      ->add_js('plugins/ckeditor/config.js', 'comum')
      ->add_js('plugins/ckeditor/build-config.js', 'comum');
  }

  public function fileupload()
  {
    if ($this->method == 'editar') {
      $this->template->add_js('plugins/image-crop/assets/lib/js/jquery.Jcrop.js', 'comum');
    }
    // Upload
    $this->template
      ->add_css('plugins/jquery-file-upload/css/single-fileupload', 'comum')
      ->add_js('plugins/jquery-file-upload/js/jquery.ui.widget.js', 'comum')
      ->add_js('plugins/jquery-file-upload/js/load-image.min.js', 'comum')
      ->add_js('plugins/jquery-file-upload/js/canvas-to-blob.min.js', 'comum')
      ->add_js('plugins/jquery-file-upload/js/jquery.iframe-transport.js', 'comum')
      ->add_js('plugins/jquery-file-upload/js/jquery.fileupload.js', 'comum')
      ->add_js('plugins/jquery-file-upload/js/jquery.fileupload-image.js', 'comum')
      ->add_js('plugins/jquery-file-upload/js/jquery.fileupload-video.js', 'comum');

    if ($this->method == 'editar') {
      $this->template->add_js('plugins/image-crop/assets/lib/js/jquery.Jcrop.js', 'comum')
        ->add_css('plugins/image-crop/assets/lib/css/jquery.Jcrop.css', 'comum');
    }
  }

  private function _js_translation()
  {
    $js = array(
      //MODULO COMUM
      'verifique_relacionamento'      => T_('Verifique o relacionamento desse registro ou entre em contato com o administrador do sistema!'),
      'erro_ordem'                    => T_('Ocorreu um erro ao atualizar a ordem, atualize a página e tente novamente!'),
      'arquivo_nao_enviado_extensao'  => T_('não será enviado.\n\nVocê não pode enviar arquivos com essa extensão'),
      'sessao_fechada'                => T_('A sessão foi fechada você será redirecionado para a tela de login!'),
      'arquivo_nao_enviado'           => T_('não será enviado.\n\nVocê não pode enviar arquivos com extensão:'),
      'campos_obrigatorios'           => T_('Há campos obrigatórios que devem ser preenchidos.'),
      'efetuar_exclusao'              => T_('Você tem certeza que deseja efetuar a exclusão?'),
      'ajax_error'                    => T_('Desculpe, ocorreu um erro. Tente novamente.'),
      'erro_inesperado'               => T_('Desculpe, ocorreu um erro inesperado.'),
      'ir_listagem'                   => T_('para ir para a listagem.'),
      'selecione_categoria'           => T_('Selecione a Categoria'),
      'selecione_editorial'           => T_('Selecione o editorial'),
      'selecione_arquivo'             => T_('Selecione um arquivo'),
      'redirecionando'                => T_('Redirecionando...'),
      'alterar_imagem'                => T_('Alterar Imagem'),
      'enviar_imagem'                 => T_('Enviar Imagem'),
      'o_arquivo'                     => T_('O arquivo'),
      'recortar'                      => T_('Recortar'),
      'excluir'                       => T_('Excluir'),
      'clique'                        => T_('Clique'),
      'aqui'                          => T_('aqui'),
      //NEGOCIOS
      'selecione_cidade'              => T_('Selecione a cidade'),
      //LOCALIZAÇÃO
      'preencha_localizacao'          => T_('Preencha corretamente os dados de localização.'),
      //ESQUECI
      'senha_caracteres'              => T_('A senha deve conter pelo menos 6 senha_caracteres'),
      'digite_email_nome'             => T_('Digite seu E-mail ou seu nome de Usuário'),
      'senhas_nao_iguais'             => T_('As senha digitadas não são iguais'),
      'nova_senha'                    => T_('Informe a sua nova senha'),
      'repira_nova_senha'             => T_('Repita a sua nova senha'),
      //CALENDARIO
      'carregando'                    => T_("Carregando..."),
      'janeiro'                       => T_('Janeiro'),
      'fevereiro'                     => T_('Fevereiro'),
      'marco'                         => T_('Março'),
      'abril'                         => T_('abril'),
      'maio'                          => T_('Maio'),
      'junho'                         => T_('Junho'),
      'julho'                         => T_('Julho'),
      'agosto'                        => T_('Agosto'),
      'setembro'                      => T_('Setembro'),
      'outubro'                       => T_('Outubro'),
      'novembro'                      => T_('Novembro'),
      'dezembro'                      => T_('Dezembro'),
      'jan'                           => T_('Jan'),
      'fev'                           => T_('Fev'),
      'mar'                           => T_('Mar'),
      'abr'                           => T_('Abr'),
      'mai'                           => T_('Mai'),
      'jun'                           => T_('Jun'),
      'jul'                           => T_('Jul'),
      'ago'                           => T_('Ago'),
      'set'                           => T_('Set'),
      'out'                           => T_('Out'),
      'nov'                           => T_('Nov'),
      'dez'                           => T_('Dez'),
      'domingo'                       => T_("Domingo"),
      'segunda'                       => T_("Segunda"),
      'terca'                         => T_("Terça"),
      'quarta'                        => T_("Quarta"),
      'quinta'                        => T_("Quinta"),
      'sexta'                         => T_("Sexta"),
      'sabado'                        => T_("Sábado"),
      'hoje'                          => T_("hoje"),
      'mes'                           => T_("mês"),
      'semana'                        => T_("semana"),
      'dia'                           => T_("dia"),
      'dia_todo'                      => T_("Dia todo"),
      //EVENTOS
      'video'                         => T_("Vídeo"),
      'titulo'                        => T_("Título"),
      'link'                          => T_("Link"),
      //GALLERY
      'excluir_imagem'                => T_("Você tem certeza que deseja excluir esta imagem?"),
      'excluir_itens'                 => T_("Você tem certeza que deseja excluir estes itens?"),
      'creditos'                      => T_("Créditos"),
      'legenda'                       => T_("Legenda"),
      'autor'                         => T_("Autor"),
      //CONTEUDO
      'menu_principal'                => T_("Menu principal"),
      //LOGIN
      'digite_senha'                  => T_("Digite a sua senha"),
      //GRUPO
      'redirecionado_10s'             => T_("Você será redirecionado em 10 segundos para a listagem de registros. Clique"),
      'ir_diretamente'                => T_("para ir diretamente."),
      //SWITCH JS
      'sim'                          => T_("Sim"),
      'nao'                          => T_("Não"),
      'pen'                          => T_("Pen"),
      //RELACIONAR CONTEÚDO NAS EMPRESAS
      'relacionar_empresa'           => T_("Deseja vincular/desvincular esse registro?"),

    );

    return json_encode($js);
  }

  /**
   * Carregamento das configurações do modulo acessado
   * @author Ramon Barros [ramon@ezoom.com.br]
   * @date   2015-03-26
   * @return void
   */
  public function load_module()
  {
    $this->title .= ' - ' . ucfirst($this->module) . '/' . ucfirst($this->class);

    switch ($this->method) {
      case 'index':
      case 'pagina':
        $this->view = "{$this->class}/listagem";
        break;
      default:
        $this->view = "{$this->class}/{$this->method}";
        break;
    }

    $file = FCPATH . 'modules' . DS . $this->module . DS . 'models' . DS . $this->class . '_m' . EXT;
    if (file_exists($file)) {
      // Carregamento do model
      $this->load->model("{$this->class}_m");

      $this->model = $this->{$this->class . '_m'};

      $this->model->module = $this->module;
      $this->model->current_module = $this->current_module;
      $this->model->class = $this->class;
      $this->model->method = $this->method;
    }

    /**
     * Verifica se a requisição não é ajax
     * X-Requested-With: XMLHttpRequest
     */
    if (!$this->input->is_ajax_request()) {
      $this->template
        ->add_css("css/{$this->module}")
        ->add_css("css/{$this->class}")
        ->add_js("js/{$this->module}")
        ->add_js("js/{$this->class}");
    }
  }

  public function pagina($pg = 1)
  {
    $this->index($pg);
  }

  /**
   * Executa um serviço caso ocorra algum problema na insersão
   * @author Ramon Barros [ramon@ezoom.com.br]
   * @date   2015-03-27
   * @param  array $data
   * @return void
   */
  public function fallback($data = array())
  {
    $this->load->helper('file');

    if (!empty($data['image'])) {
      $this->_delete_file($data['image']);
    }
    if (!empty($data['file'])) {
      foreach ($data['file'] as $name => $file) {
        // Multi-linguas
        if (is_array($file)) {
          foreach ($file as $id_language => $fl) {
            $this->_delete_file($fl);
          }
        } else {
          $this->_delete_file($file);
        }
      }
    }
    if (!empty($data['archive'])) {
      foreach ($data['archive'] as $name => $file) {
        // Multi-linguas
        if (is_array($file)) {
          foreach ($file as $id_language => $fl) {
            $this->_delete_file($fl);
          }
        } else {
          $this->_delete_file($file);
        }
      }
    }
    if (!empty($data['gallery'])) {
      foreach ($data['gallery'] as $name => $file) {
        // Multi-linguas
        $file = $file['image'];
        if (is_array($file)) {
          foreach ($file as $id_language => $fl) {
            $this->_delete_file($fl);
          }
        } else {
          $this->_delete_file($file);
        }
      }
    }
  }

  private function _delete_file($file)
  {
    $this->db->select('*')->from('ez_file')->where('id', $file);
    $result = $this->db->get()->row();

    if (isset($result) && isset($result->file)) {
      $module = dirname(FCPATH) . DS . 'userfiles' . DS . $this->module . DS . $result->file;
      $class = dirname(FCPATH) . DS . 'userfiles' . DS . $this->module . DS . $this->class . DS . $result->file;
      delete_file($module);
      delete_file($class);

      $this->db->where(array('id' => $file))->delete('ez_file');
    }
  }

  public function editar($id)
  {
    //Busca item que vai editar
    $id || show_404();
    $params = array_merge(array('id' => $id), $this->params);
    $item = $this->model->get($params);
    $item || show_404();

    $this->template
      ->set('id', $id)
      ->set('item', $item);
  }

  protected function formulario($id = false, $build = TRUE)
  {
    //Dados que aparecem em cadastrar e editar
    $this->template
      ->set('breadcrumb_route', array($this->current_module->slug => $this->current_module->name, ucfirst($this->method)))
      ->set('title', SITE_NAME . ' - ' . ucfirst($this->method) . ' ' . $this->current_module->name);
    if ($build) {
      $this->template->build('formulario');
    }
  }

  /**
   * Adicionado um registro
   * @author Ramon Barros [ramon@ezoom.com.br]
   * @date   2015-03-27
   */
  public function add()
  {
    try {
      if ($this->model->insert($this->input->post())) {
        $this->json = array(
          'status' => true,
          'classe' => 'success',
          'message' => T_('Registro inserido com sucesso!'),
          'redirect' => true,
          'redirectModule' => $this->slug
        );
      } else {
        $this->fallback($this->input->post());
        $error_array = $this->form_validation->error_array();
        $errors = (is_array($error_array) && !empty($error_array)) ? current($error_array) : T_('Erro ao cadastrar as informações.');
        throw new Exception($errors);
      }
    } catch (Exception $e) {
      $this->fallback($this->input->post());
      $this->json = array(
        'status' => false,
        'classe' => 'error',
        'message' => $e->getMessage() . (ENVIRONMENT == 'development' ? ' - ' . $e->getFile() . ' - linha:' . $e->getLine() : ''),
        'redirect' => false
      );
      log_message('error', print_r($e, true));
    }
    $this->output->set_output(json_encode($this->json));
  }

  /**
   * Atualiza registro
   * @author Ramon Barros [ramon@ezoom.com.br]
   * @date   2015-03-27
   */
  public function edit($id)
  {
    try {
      if ($this->model->update($id, $this->input->post())) {
        $this->json = array(
          'status' => true,
          'classe' => 'success',
          'message' => T_('Registro atualizado com sucesso!'),
          'redirect' => true,
          'redirectModule' => $this->slug
        );
      } else {
        $this->fallback($this->input->post());
        $error_array = $this->form_validation->error_array();
        $errors = (is_array($error_array) && !empty($error_array)) ? current($error_array) : T_('Erro ao cadastrar as informações.');
        throw new Exception($errors);
      }
    } catch (Exception $e) {
      $this->fallback($this->input->post());
      $this->json = array(
        'status' => false,
        'classe' => 'error',
        'message' => $e->getMessage() . (ENVIRONMENT == 'development' ? ' - ' . $e->getFile() . ' - linha:' . $e->getLine() : ''),
        'redirect' => false
      );
      log_message('error', print_r($e, true));
    }
    $this->output->set_output(json_encode($this->json));
  }

  /**
   * Excluir um registro
   * @author Diogo Taparello [diogo@ezoom.com.br]
   * @date   2016-04-01
   * @param  integer $id
   * @return void
   */
  public function delete($id)
  {
    try {
      if ($this->input->post('delete') == 'true' && $id) {
        if ($this->model->delete($id, $this->input->post())) {
          $this->json = array(
            'status' => true,
            'classe' => 'success',
            'message' => T_('Exclusão efetuada com sucesso!')
          );
        } else {
          throw new Exception(T_("Erro ao tentar excluir o registro!"));
        }
      } else {
        throw new Exception(T_("ID de exclusão inválido"));
      }
    } catch (Exception $e) {
      $this->json = array(
        'status' => false,
        'classe' => 'error',
        'message' => $e->getMessage()
      );
      log_message('error', print_r($e, true));
    }
    $this->output->set_output(json_encode($this->json));
  }

  /**
   * Exclusão multipla
   * @author Ramon Barros [ramon@ezoom.com.br]
   * @date   2015-03-27
   * @return void
   */
  public function delete_multiple()
  {
    try {
      $this->load->library('form_validation');

      $this->form_validation->set_rules('id', 'ID', 'trim|required');

      $this->form_validation->set_message('id', T_('IDs de exclusão inválidos.'));

      if ($this->form_validation->run() === true) {
        if ($this->model->delete_multiple($this->input->post('id'), $this->input->post())) {
          $response = array(
            'status' => true,
            'classe' => 'success',
            'message' => T_('Exclusão efetuada com sucesso!'),
            'id' => $this->input->post('id')
          );
        } else {
          throw new Exception(T_("Erro ao excluir os registros!"));
        }
      } else {
        throw new Exception(T_("ID de exclusão inválido"));
      }
    } catch (Exception $e) {
      $response = array(
        'status' => false,
        'classe' => 'error',
        'message' => $e->getMessage()
      );
      log_message('error', print_r($e, true));
    }

    $this->output->set_output(json_encode($response));
  }

  public function sort()
  {
    $this->session->keep_flashdata();
    if (method_exists($this->model, 'sort')) {
      $this->model->sort($this->input->post());

      $response = array(
        'status' => true,
        'classe' => 'warning',
        'message' => T_('Ordem alterada com sucesso'),
        'redirect' => true,
        'redirectModule' => site_url($this->module)
      );
    } else {
      $response = array(
        'status' => false,
        'classe' => 'error',
        'message' => T_('O model do modulo') . ' ' . $this->module . ' ' . T_('não possui o método sort()'),
        'redirect' => true,
        'redirectModule' => site_url($this->module)
      );
    }

    echo json_encode($response);
  }

  public function active()
  {
    $this->session->keep_flashdata();
    $this->load->library('form_validation');
    $this->form_validation->set_rules('id', 'ID', 'trim|required|integer');
    $this->form_validation->set_rules('actived', T_('Status'), 'trim|required');
    $this->form_validation->set_message('id', T_('ID de ativação inválido.'));

    if ($this->form_validation->run() === true) {
      if (method_exists($this->model, 'toggleStatus')) {
        if ($this->model->toggleStatus($this->input->post())) {
          if ($this->input->post('actived') == 'true') {
            $class = 'success';
            $message = T_('Registro Ativo!');
          } else {
            $class = 'warning';
            $message = T_('Registro Inativo!');
          }

          $response = array(
            'status' => true,
            'classe' => $class,
            'message' => $message
          );
        } else {
          $response = array(
            'status' => false,
            'classe' => 'error',
            'message' => T_('Ocorreu um erro ao tentar alterar. Tente novamente mais tarde!')
          );
        }
      } else {
        $response = array(
          'status' => false,
          'classe' => 'error',
          'message' => T_('O model do modulo') . ' ' . $this->module . ' ' . T_('não possui método toggleStatus()')
        );
      }
    } else {
      $errors = array_values($this->form_validation->error_array());
      $response = array('status' => false, 'classe' => 'error', 'message' => $errors[0]);
    }

    echo json_encode($response);
  }

  public function changeOrder()
  {
    $order = $this->session->flashdata('order_' . $this->class);
    $type = 'asc';

    if ($this->session->flashdata('order_' . $this->class)) {
      $type =  ($order['type'] == 'desc') ? 'asc' : 'desc';
    }

    $this->session->set_flashdata('order_' . $this->class, false);
    $this->session->set_flashdata('order_' . $this->class, array('type' => $type, 'field' => $this->input->post('field')));
    echo json_encode(array('status' => true));
  }

  public function removeOrder()
  {
    $this->session->set_flashdata('order_' . $this->class, false);
    echo json_encode(array('status' => true));
  }

  /**
   * Retorna um array de dados em formato Json.
   *
   * @author Detley Oliveira [detley@ezoom.com.br]
   * @date   2016-08-12
   * @param  array     $data
   * @return void
   */
  public function toJson($data)
  {
    header('Content-Type: application/json');

    try {
      echo json_encode($data, JSON_PRETTY_PRINT);
    } catch (Exception $e) {
      echo json_encode($data);
    }
  }
}
