<?php (defined('BASEPATH')) or exit('No direct script access allowed');

/**
 * Controller
 *
 * @package ezoom
 * @subpackage Empresas
 * @category Controller
 * @author Diogo Taparello
 * @copyright 2016 Ezoom
 */
class Empresas extends MY_Controller
{
  protected $default_colors;

  public function __construct()
  {
    parent::__construct();
    $this->load->model('empresas_m');
    // Mantem sessão flashdata
    $this->session->keep_filter(array('administracao/empresas'));

    $this->default_colors = $this->config->item('default_colors');
    $this->template->set('default_colors', $this->default_colors);
  }

  public function index($pg = 1)
  {
    // Set Filtro
    $this->session->register_filter('administracao/empresas', 'administracao/empresas');
    $filter = ($this->session->flashdata('filter')) ? $this->session->flashdata('filter') : false;
    $this->load->library('pagination');
    $search = $filter ? $filter['search'] : false;

    $total = $this->empresas_m->get_all(array('search' => $search, 'total' => TRUE));
    $max = ($filter) ? (isset($filter['show']) ? $filter['show'] : 10) : 10;
    $start = ($pg - 1) * $max;

    $pagination = $this->pagination->init(array(
      'url' => site_url('administracao/empresas'),
      'total' => $total,
      'max' => $max,
      'segment' => 4 // Segment no qual o numero da pagina estará
    ));

    $totalPage = (($start + $max) > $total) ? $total : ($start + $max);
    $showing = '';

    if ($total > 0)
      $showing = T_('Exibindo resultados ') . ' ' . ($start + 1) . ' - ' . $totalPage . ' ' . T_(' de um total de ') . ' ' . $total;
    else if ($search)
      $showing = T_('Não há resultados para esta busca.');

    $itens = $this->empresas_m->get_all(array('search' => $search, 'start' => $start, 'max' => $max));

    $this->template
      ->add_css('css/empresas')
      ->add_js('js/empresas')
      ->set('title', SITE_NAME . ' - ' . T_('Empresas'))
      ->set('breadcrumb_route', array(T_('Empresas')))
      ->set('items', $itens)
      ->set('paginacao', $pagination)
      ->set('search', $search)
      ->set('show', $max)
      ->set('total', $total)
      ->set('showing', $showing)
      ->build('empresas/listagem');
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
    $this->fileUpload();
    $this->ckeditor();

    if ($id) {
      $item = $this->empresas_m->get(array('id' => $id));
      $item || $this->auth->deny_access(T_('Você não tem permissão para realizar esta operação.'), 'empresas');
      $item->colors = json_decode($item->colors);

      $this->template
        ->set('title', SITE_NAME . ' - ' . T_('Empresas'))
        ->set('breadcrumb_route', array('administracao/empresas' => T_('Empresas'), T_('Editar')))
        ->set('id', $id)
        ->set('item', $item);
    } else {
      $this->template
        ->set('title', SITE_NAME . ' - ' . T_('Cadastrar Empresas'))
        ->set('breadcrumb_route', array('administracao/empresas' => T_('Empresas'), T_('Cadastrar')));
    }

    $languages_by_company = $this->comum_m->get_languages(isset($item) ? $item : NULL);
    $companies = $this->empresas_m->get_all(array('id' => $id, 'reverse' => TRUE));
    $countries = $this->comum_m->get_countries();
    $states = $this->comum_m->get_state();

    $this->template
      ->add_css('css/empresas')
      ->add_css('css/endereco', 'endereco')
      ->add_js('plugins/jquery.inputmask.bundle.min', 'comum')
      ->add_js('plugins/colorpicker2/js/bootstrap-colorpicker', 'comum')
      ->add_css('plugins/colorpicker2/css/colorpicker', 'comum')
      ->add_js('js/empresas')
      ->add_js('js/endereco', 'endereco')
      ->add_js('https://maps.googleapis.com/maps/api/js?key=' . GMAPS_KEY)
      ->set('countries', $countries)
      ->set('companies', $companies)
      ->set('languages_by_company', $languages_by_company)
      ->set('states', $states)
      ->build('empresas/formulario');
  }

  public function add()
  {
    $post = $this->input->post();

    //Geramos o css da empresa
    if ($this->config->item('multi_company_colors')) {
      $post['css_file'] = $this->generateCss($post);
      $post['colors'] = json_encode($post['colors']);
    }

    if ($this->empresas_m->insert($post)) {
      $response = array('status' => true, 'classe' => 'success', 'message' => T_('Registro inserido com sucesso!'), 'redirect' => true, 'redirectModule' => 'administracao/empresas');
    } else {
      $this->fallback($this->input->post());
      $response = array('status' => false, 'classe' => 'error', 'message' => T_('Ocorreu um erro inesperado no cadastro'), 'redirect' => false);
    }

    echo json_encode($response);
  }

  public function edit($id)
  {
    if ($id) {
      $post = $this->input->post();

      //Geramos o css da empresa
      if ($this->config->item('multi_company_colors')) {
        $post['css_file'] = $this->generateCss($post);
        $post['colors'] = json_encode($post['colors']);
      }

      if ($this->empresas_m->update($post, $id)) {
        $response = array('status' => true, 'classe' => 'success', 'message' => T_('Registro editado com sucesso!'), 'redirect' => true, 'redirectModule' => 'administracao/empresas');
      } else {
        $this->fallback($this->input->post());
        $response = array('status' => false, 'classe' => 'error', 'message' => T_('Ocorreu um erro inesperado na edição'), 'redirect' => false);
      }
    } else {
      $response = array('status' => false, 'classe' => 'error', 'message' => T_('Id Inválido'), 'redirect' => false);
    }

    echo json_encode($response);
  }


  public function sort()
  {
    $this->empresas_m->sort($_POST['item']);

    $response = array(
      'status' => true,
      'classe' => 'warning',
      'message' => T_('Ordem alterada com sucesso'),
      'redirect' => true,
      'redirectModule' => site_url('empresas')
    );

    echo json_encode($response);
  }

  public function fallback($data)
  {
    $image = (isset($data['image']) && strlen($data['image']) > 0) ? $data['image'] : null;
    if ($image) {
      @unlink(dirname(FCPATH) . '/userfiles/empresas/' . $image);
    }
    $avatar = (isset($data['avatar']) && strlen($data['avatar']) > 0) ? $data['avatar'] : null;
    if ($avatar) {
      @unlink(dirname(FCPATH) . '/userfiles/empresas/' . $avatar);
    }
  }

  /**
   * Gera o css com as cores da empresa
   * @author Fábio Neis [fabio@ezoom.com.br]
   * @date   2017-11-30
   * @param  $post     Recebe o post por referencia pois pode corrigir as cores
   * @return [string]
   */
  public function generateCss(&$post)
  {
    $this->load->library('lessc');
    $this->lessc->setOption('compress', true);

    if (!$post['colors']['primary'])
      $post['colors']['primary'] = $this->default_colors['primary'];
    else if (strpos($post['colors']['primary'], '#') === false)
      $post['colors']['primary'] = '#' . $post['colors']['primary'];

    if (!$post['colors']['gradient']['from'])
      $post['colors']['gradient']['from'] = $this->default_colors['gradient_from'];
    else if (strpos($post['colors']['gradient']['from'], '#') === false)
      $post['colors']['gradient']['from'] = '#' . $post['colors']['gradient']['from'];

    if (!$post['colors']['gradient']['to'])
      $post['colors']['gradient']['to'] = $this->default_colors['gradient_to'];
    else if (strpos($post['colors']['gradient']['to'], '#') === false)
      $post['colors']['gradient']['to'] = '#' . $post['colors']['gradient']['to'];

    $svg = $this->generateSvgGradient('-45', $post['colors']['gradient']['from'], $post['colors']['gradient']['to']);

    $import = array(
      '@import "' . FCPATH . 'modules' . DS . 'comum' . DS . 'assets' . DS . 'less' . DS . '_mixins.less"',
    );

    $filename = md5($post['email'] . $post['fantasy_name'] . $post['slug'] . $post['domain']);
    $content = implode(';', $import) . ';
            @primaryColor: ' . $post['colors']['primary'] . ';
            @gradientFrom: ' . $post['colors']['gradient']['from'] . ';
            @gradientTo: ' . $post['colors']['gradient']['to'] . ';
            @gradientSvg: "' . base64_encode($svg) . '";

            //.' . $filename . ' {
                ' . file_get_contents(FCPATH . 'modules' . DS . 'administracao' . DS . 'assets' . DS . 'less' . DS . 'template' . DS . 'main.less') . '
            //}
        ';
    $css = $this->lessc->parse($content);

    $res = null;

    $dir = dirname(FCPATH) . ((ENVIRONMENT == 'development') ? DS . 'framework-ezoom-codeigniter' : '') . DS . 'userfiles' . DS . 'empresas' . DS;

    if (!is_dir($dir))
      mkdir($dir, 0777, true);

    if (file_put_contents($dir . $filename . '.css', $css))
      $res = $filename . '.css';

    return $res;
  }

  private function generateSvgGradient($deg, $from, $to)
  {
    $svg = '<?xml version="1.0"?>';
    ob_start();
?>
    <svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 1 1" preserveAspectRatio="none">
      <linearGradient id="linear-gradient" gradientUnits="userSpaceOnUse" x1="0%" y1="0%" x2="100%" y2="100%">
        <stop offset="0%" stop-color="<?php echo $from; ?>" stop-opacity="1" />
        <stop offset="100%" stop-color="<?php echo $to; ?>" stop-opacity="1" />
      </linearGradient>
      <rect x="0" y="0" width="1" height="1" fill="url(#linear-gradient)" />
    </svg>
<?php
    $svg .= ob_get_clean();
    return $svg;
  }
}
