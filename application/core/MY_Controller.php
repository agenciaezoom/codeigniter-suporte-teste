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

  protected $company;
  protected $class;
  protected $module;
  protected $method;
  protected $current_lang;
  protected $langLinks;
  protected $currentDbRoute;
  protected $isRun;
  protected $isMobile;
  protected $sendTo = array();
  protected $i18n = array();

  public function __construct($isRun = FALSE)
  {
    parent::__construct();
    $this->isRun = $isRun;

    $this->company = isset($_SESSION['company']) ? (object)$_SESSION['company'] : new StdClass;
    $this->company->meta_title = isset($this->company->meta_title) && $this->company->meta_title ? $this->company->meta_title : 'Tecnomaq';

    $this->class = $this->router->fetch_class();
    $this->module = $this->router->fetch_module();
    $this->method = $this->router->fetch_method();
    $this->current_lang = $this->lang->lang();

    $this->load->library('template');

    if (ENVIRONMENT == 'development') {
      $this->load->library('whoops');
      $this->output->enable_profiler($this->config->item('enable_profiler'));
    }

    if ($this->input->is_ajax_request()) {
      $this->output->enable_profiler(false);
    }

    $this->load->helper('language');
    $this->load->library('user_agent');
    $this->load->model('comum/comum_m');

    if ($this->config->item('routes_db')) {
      $this->langLinks = $this->router->get_routes();
      $this->currentDbRoute = $this->router->get_current_route();

      if (empty($this->currentDbRoute) && $this->module == 'home') {
        $urlComplement = array();
        foreach ($this->config->config['supported_lang_id'] as $key => $id_language) {
          $urlComplement[$key] = $key . '/home';
        }

        $this->template->set('urlComplement', $urlComplement);
      }

      $this->template
        ->set('langLinks', $this->langLinks)
        ->set('currentDbRoute', $this->currentDbRoute);
    }

    if (!$this->isRun) {
      if ($this->config->item('multi_company') && $this->db->autoinit) {
        $this->template->set('all_companies', $this->comum_m->getActiveCompanies($this->company->id));
      }

      if ($this->config->item('support_mobile')) {
        if ($this->session->userdata('force_desktop')) {
          $this->isMobile = FALSE;
        } else {
          $this->isMobile = $this->agent->is_mobile();
        }
      } else {
        $this->isMobile = FALSE;
      }

      $this->load->helper('lazyload');

      if (!$this->input->is_ajax_request()) {
        if ($this->db->autoinit)
          $this->_seo();

        $this->template
          ->set('lang', $this->current_lang)
          ->set('mobile', $this->isMobile)
          ->set('isMobile', $this->agent->is_mobile())
          ->set('company', $this->company)
          ->set('class', $this->class)
          ->set('module', $this->module)
          ->set('version', 'v' . $this->config->item('version'))
          ->set('csrf_test_name', $this->security->get_csrf_hash())
          ->set('i18n', $this->_get_js_translation());

        $this->template->add_css('//fonts.googleapis.com/css?family=Montserrat:100,300,400,500,600,700&display=swap');

        if (ENVIRONMENT == 'production') {
          $this->template->add_js('//cdnjs.cloudflare.com/ajax/libs/modernizr/2.6.2/modernizr.min.js')
            ->add_js('//ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js');
        } else {
          $this->template->add_js('plugins/modernizr-2.6.2.min.js', 'comum', 'body')
            ->add_js('js/jquery-3.1.1.min.js', 'comum', 'body');
        }

        $common_content = $this->comum_m->getPageContent(array(
          'area' => 'Comum'
        ));

        $loadLang = $this->current_lang == 'pt' ? 'pt-br' : $this->current_lang;

        $this->template
          ->add_css('css/main.css', 'comum')
          ->add_js('plugins/plugins', 'comum')
          ->add_js('plugins/select2/i18n/' . $loadLang, 'comum')
          ->add_js('plugins/slick/slick.min.js', 'comum')
          ->add_js('plugins/magnificpopup/jquery.magnific-popup.min', 'comum')
          ->add_js('js/main.js', 'comum')
          ->set('common_content', $common_content)
          ->set_partial('header', 'header', 'comum')
          ->set_partial('footer', 'footer', 'comum')
          ->set_partial('breadcrumb', 'breadcrumb', 'comum');
      }
    }
  }

  private function _seo()
  {
    /////////////
    // JSON+LD //
    /////////////
    $jsonld = array(
      "@context"  => "http://schema.org",
      "@type"     => "Organization",
      "name"      => $this->company->meta_title,
      "telephone" => $this->company->phone,
      "url"       => base_url(),
      "logo"      => base_img('logo.png'),
      "address"   => array(
        "@type"             => "PostalAddress",
        "addressCountry"    => "BR",
        "addressLocality"   => $this->company->city,
        "addressRegion"     => $this->company->state,
        "streetAddress"     => $this->company->address . ', ' . $this->company->number . ', ' . $this->company->district
      ),
      'sameAs'    => array()
    );
    if (isset($this->company->facebook) && $this->company->facebook) {
      $jsonld['sameAs'][] = $this->company->facebook;
    }
    if (isset($this->company->twitter) && $this->company->twitter) {
      $jsonld['sameAs'][] = $this->company->twitter;
    }
    if (isset($this->company->youtube) && $this->company->youtube) {
      $jsonld['sameAs'][] = $this->company->youtube;
    }
    if (isset($this->company->instagram) && $this->company->instagram) {
      $jsonld['sameAs'][] = $this->company->instagram;
    }
    if (empty($jsonld['sameAs']))
      unset($jsonld['sameAs']);
    $this->template->add_json_ld($jsonld);

    if (!empty($this->currentDbRoute)) {
      $metas = $this->comum_m->getMetas($this->currentDbRoute[$this->current_lang]);
      $this->template->set('title', $metas->seo_title . ($metas->seo_title ? ' - ' : '') . $this->company->meta_title);
      if ($metas->seo_keywords != '')
        $this->template->add_metadata('keywords', $metas->seo_keywords);
      else if ($this->company->meta_keywords)
        $this->template->add_metadata('keywords', $this->company->meta_keywords);
      if ($metas->seo_description != '') {
        $this->template->add_metadata('description', $metas->seo_description);
        $this->template->add_metadata('og:description', $metas->seo_description, 'property');
      } else if ($this->company->meta_description) {
        $this->template->add_metadata('description', $this->company->meta_description);
        $this->template->add_metadata('og:description', $this->company->meta_description, 'property');
      }
    } else {
      if (isset($this->company->meta_title) && $this->company->meta_title)
        $this->template->set('title', $this->company->meta_title);
      if (isset($this->company->meta_keywords) && $this->company->meta_keywords)
        $this->template->add_metadata('keywords', $this->company->meta_keywords);
      if (isset($this->company->meta_description) && $this->company->meta_description) {
        $this->template->add_metadata('description', $this->company->meta_description);
        $this->template->add_metadata('og:description', $this->company->meta_description, 'property');
      }
    }

    $this->template->add_metadata('og:image', site_url('userfiles/paginas/chicago-22-2.jpg'), 'property')
      ->add_metadata('og:image:width', 800, 'property')
      ->add_metadata('og:image:height', 418, 'property')
      ->add_metadata('og:locale', 'pt_BR', 'property')
      ->add_metadata('og:url', site_url($this->uri->uri_string()), 'property')
      ->add_metadata('og:site_name', $this->company->meta_title, 'property')
      ->add_metadata('og:title', $this->company->name, 'property')
      ->add_metadata('og:type', 'website', 'property');
  }

  protected function _initPagination($params)
  {
    $default = array(
      'segment'   => 2,
      'max'       => 8,
      'links'     => 3,
      'query'     => FALSE
    );

    $params = array_merge($default, $params);

    $this->load->library('pagination');

    $config['num_links']        = $params['links'];
    $config['full_tag_open']    = "<ul class='pagination'>";
    $config['full_tag_close']   = "</ul>";
    $config['first_tag_open']   = "<li>";
    $config['last_tag_open']    = "<li class='dots'>...</li><li>";
    $config['first_tag_close']  = "</li><li class='dots'>...</li>";
    $config['last_tag_close']   = "</li>";
    $config['next_tag_open']    = $config['prev_tag_open']  = "<li class='prevNext'>";
    $config['next_tag_close']   = $config['prev_tag_close'] = "</li>";
    $config['first_link']       = T_('primeira');
    $config['last_link']        = T_('última');
    $config['next_link']        = '';
    $config['prev_link']        = '';
    $config['num_tag_open']     = "<li>";
    $config['num_tag_close']    = "</li>";
    $config['cur_tag_open']     = "<li class='active'><span>";
    $config['cur_tag_close']    = "</span></li>";
    $config['per_page']         = $params['max'];
    $config['use_page_numbers'] = TRUE;

    $config['base_url']             = trim($params['url'], '/') . '/';
    $config['total_rows']           = $params['total'];
    $config['uri_segment']          = $params['segment'];
    $config['reuse_query_string']   = TRUE;

    if ($params['query']) {
      $config['page_query_string']    = TRUE;
      $config['query_string_segment'] = 'pg';
    } else {
      $config['first_url']            = trim($config['base_url'], '/') . "/1";
    }

    $this->pagination->initialize($config);
    return $this->pagination->create_links();
  }

  protected function fix_slug($value)
  {
    return str_replace('_', '-', $value);
  }

  private function _get_js_translation()
  {
    $js = array_merge(
      array(
        'ajax_error_message' => T_('Desculpe, ocorreu um problema inesperado. Confira os campos preenchidos no formulário e tente novamente.'),
        'ajax_error_title' => T_('Ocorreu um erro'),
        'close' => T_('Fechar')
      ),
      $this->i18n
    );

    return json_encode($js);
  }
}
