<?php (defined('BASEPATH')) or exit('No direct script access allowed');

/* load the MX_Router class */
require APPPATH . "third_party/MX/Router.php";
require(BASEPATH . 'database/DB' . EXT);

/**
 * CodeIgniter MY_Router
 *
 * Rotas da aplicação
 *
 * @package     CodeIgniter
 * @author      Ezoom
 * @subpackage  Loader
 * @category    Loader
 * @link        http://ezoom.com.br
 * @copyright  Copyright (c) 2008, Ezoom
 * @version 1.0.0
 *
 */
class MY_Router extends MX_Router
{
  private $db_routing = array();
  private $db_lang_route = array();
  private $db_current_route = array();
  private $db;

  public function __construct()
  {
    session_start();
    parent::__construct();
  }

  /**
   * Set the route mapping
   *
   * This function determines what should be served based on the URI request,
   * as well as any "routes" that have been set in the routing config file.
   *
   * @access  private
   * @return void
   */

  /**
   * Gera o array de rotas para o CodeIgniter.
   * Entretanto, se a flag route_db no config for TRUE, ira buscar as rotas do banco e setar o idioma corretamente de acordo com a url atual;
   * @author Fabio Bachi [fabio.bachi@ezoom.com.br]
   * @author Ralf da Rocha [ralf@ezoom.com.br]
   * @date   2014-11-28
   */
  public function _set_routing()
  {
    // Are query strings enabled in the config file?  Normally CI doesn't utilize query strings
    // since URI segments are more search-engine friendly, but they can optionally be used.
    // If this feature is enabled, we will gather the directory/class/method a little differently
    $segments = array();
    if ($this->config->item('enable_query_strings') === true and isset($_GET[$this->config->item('controller_trigger')])) {
      if (isset($_GET[$this->config->item('directory_trigger')])) {
        $this->set_directory(trim($this->uri->_filter_uri($_GET[$this->config->item('directory_trigger')])));
        $segments[] = $this->fetch_directory();
      }

      if (isset($_GET[$this->config->item('controller_trigger')])) {
        $this->set_class(trim($this->uri->_filter_uri($_GET[$this->config->item('controller_trigger')])));
        $segments[] = $this->fetch_class();
      }

      if (isset($_GET[$this->config->item('function_trigger')])) {
        $this->set_method(trim($this->uri->_filter_uri($_GET[$this->config->item('function_trigger')])));
        $segments[] = $this->fetch_method();
      }
    }

    // Fetch the complete URI string
    $this->uri->_fetch_uri_string();

    // Do we need to remove the URL suffix?
    $this->uri->_remove_url_suffix();

    // Compile the segments into an array
    $this->uri->_explode_segments();

    // Se a flag do config for TRUE, ignora TODAS as rotas do arquivo config/routes.php e busca apenas as encontradas no banco.
    $this->db = &DB();

    if ((isset($_SESSION['base_url']) && $_SESSION['base_url'] != $this->config->item('base_url')) || !isset($_SESSION['base_url'])) {
      unset($_SESSION['company']);
    }

    if ($this->config->item('routes_db') && $this->db->autoinit) {

      $this->routes = array();
      $this->db_routing = array();

      $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');

      if ($this->config->item('multi_company')) {
        $this->new_alias = $this->config->item('base_url');
        $this->new_alias = str_replace('http://www.', 'http://', $this->new_alias);
        $main_alias = unserialize(WEBSITE_ALIAS);

        $old_company = isset($_SESSION['company']) ? $_SESSION['company']['id'] : FALSE;

        // Se não for requisição ajax OU não estiver fazendo a troca de línguas... procura company
        if ($this->uri->segment(0) != 'lang') {
          // Caso esteja acessando de um site externo
          // if (!in_array($this->new_alias, $main_alias)) {
          //   $this->_setCompany(array('company.domain' => $this->new_alias));
          //   // Se não encontrou a empresa desse site, direciona para o site principal (escolha de empresas)
          //   if (!isset($_SESSION['company']) && !$isAjax) {
          //     header('Location: ' . $main_alias[0]);
          //   }
          // } else {
          $update = TRUE;
          // Se tem slug
          if ($this->uri->segment(0) && $this->uri->segment(0) != '') {
            // Confere se é pra trocar de empresa
            if ($this->uri->segment(0) != 'application' && (!isset($_SESSION['company']) || $_SESSION['company']['slug'] != $this->uri->segment(0))) {
              $this->_setCompany(array('company.slug' => $this->uri->segment(0)));
              $update = FALSE;
            }
            // Se não encontrou nada, pega a principal
            if (!isset($_SESSION['company'])) {
              $this->_setCompany();
              $update = FALSE;
            }
          } else {
            // Se não tem slug, pega a principal
            $this->_setCompany();
            $update = FALSE;
          }
          // Atualiza dados
          if (isset($_SESSION['company']) && $update) {
            $this->_setCompany(array('company.slug' => $_SESSION['company']['slug']));
          }
          // }
          if (!isset($_SESSION['company']) && !$isAjax) {
            header('Location: ' . $main_alias[0]);
            exit;
          }
        }
      } else {
        $old_company = isset($_SESSION['company']) ? $_SESSION['company']['id'] : FALSE;
        $this->_setCompany();
        if (!$old_company || $old_company != $_SESSION['company']['id']) {
          $this->_setCookie($_SESSION['company']['language_main']);
        }
      }

      // Configurações de línguas suportadas
      $lang = $this->_getLangs($_SESSION['company']);
      $this->config->set_item('supported_lang', $lang['codes']);
      $this->config->set_item('language', reset($lang['codes']));
      $this->config->set_item('language_abbr', key($lang['codes']));
      $this->config->set_item('supported_lang_id', $lang['ids']);

      // Configura língua atual da tabela description da
      if ($this->db->autoinit && isset($_SESSION['company'])) {
        if (!isset($_SESSION['user_lang'])) {
          $_SESSION['user_lang'] = isset($_SESSION['company']['language_main']) ? $_SESSION['company']['language_main'] : reset(array_keys($this->supported_lang));
        }
        $_SESSION['company'] = array_merge($this->_getCompanyInfo(), $_SESSION['company']);
      }

      if ($this->config->item('multi_company')) {
        // Confere se houve mudança de site
        $company_change = $old_company != $_SESSION['company']['id'];

        //Se nao tiver a sessao setada OU houve uma mudança de site que não suporta a língua atual, busca o idioma principal.
        if (!isset($_SESSION['user_lang']) || ($company_change && !isset($lang['codes'][$_SESSION['user_lang']]))) {
          $this->_setCookie($_SESSION['company']['language_main']);
        }
      }

      // Confere troca de línguas
      if ($this->uri->segment(0) == 'lang' && $uri_abbr = $this->uri->segment(1)) {
        if (isset($lang['codes'][$uri_abbr])) {
          $this->_setCookie($uri_abbr);
          /* remove the invalid abbreviation */
          $url = preg_replace("|^\/?lang\/$uri_abbr\/?|", '', $this->uri->uri_string());
          /* redirect */
          header('Location: ' . $this->config->item('base_url') . $url);
          exit;
        }
      }

      // Beleza, agora vamos para as tradicionais rotas multilinguas:
      $this->db_routing['default_controller'] = "home";
      $this->db_routing['404_override'] = 'comum/index';

      // Busca TODAS as routes do banco, de TODOS os idiomas do site.
      $this->db->select('ez_route.id, code, url, method, url_complement, key')
        ->from('ez_route')
        ->join('ez_route_description AS description', 'description.id_route = ez_route.id', 'INNER')
        ->join('ez_language', 'ez_language.id = description.id_language', 'INNER')
        ->where_in('ez_language.id', explode(',', $_SESSION['company']['languages_site']))
        ->where('ez_route.status', '1')
        ->where('ez_route.id_company', $_SESSION['company']['id_company'])
        ->where(array(
          'ez_language.status' => '1'
        ))
        ->order_by('ez_route.order_by');
      $query = $this->db->get();
      $routeId = null;
      $relevance = 0;
      $allRoutes = array();

      if ($query->num_rows()) {
        // Percorre todas as routes, procurando a URL atual com o idioma atual.
        // Caso nao encontre com o idioma atual, seta o Session para o idioma da URL atual.
        foreach ($query->result() as $key => $data) {
          $dbUrl = str_replace('_', '-', $data->url);
          $dbUrl = explode('/', rtrim($dbUrl, '/'));
          $compareString = '';
          foreach ($dbUrl as $k => $part) {
            $i = $_SESSION['company']['sufix'] ? $k + 1 : $k;
            $compareString .= str_replace('_', '-', $k == 0 && !$this->uri->segment($i) ? '' : $this->uri->segment($i)) . '/';
          }
          $compareString = rtrim($compareString, '/');
          // Compara a URL atual com a URL da route percorrida do banco.
          // Caso a mesma URL seja igual para multiplos idiomas, pega a do idioma atual.
          // Se o idioma atual nao for uma dessas multiplas linguas, pega a primeira encontrada.
          if ($compareString !== '' && $compareString === implode('/', $dbUrl) && count($dbUrl) >= $relevance) {
            $relevance = count($dbUrl);
            $first = (!isset($first)) ? $data->code : ($data->code == $_SESSION['user_lang'] ? $data->code : $first);

            if ($data->code == $_SESSION['user_lang']) {
              $goToLang = $data->code;
            }

            $routeId = $data->id;
          }

          $allRoutes[$data->code][$_SESSION['company']['sufix'] . rtrim($data->url . $data->url_complement, '/')] = $data->method;

          //cria rota sem complemento quando tem um complemento só
          if ($data->url_complement && $data->url_complement != '' && strlen($data->url_complement) == 6 && !isset($allRoutes[$data->code][$_SESSION['company']['sufix'] . rtrim($data->url, '/')])) {
            $allRoutes[$data->code][$_SESSION['company']['sufix'] . rtrim($data->url, '/')] = str_replace('$1', '', $data->method);
          }

          $this->db_lang_route[$data->code][$data->key] = $data->url;
        }
        if ($_SESSION['company']['sufix'] && trim($_SESSION['company']['sufix'], '/') == $this->uri->segment(0) && (!$this->uri->segment(1) || $this->uri->segment(1) == '')) {
          $goToLang = $_SESSION['user_lang'];
        } else {
          $goToLang = isset($goToLang) ? $goToLang : (isset($first) ? $first : false);
        }

        if ($goToLang) {
          $this->_setCookie($goToLang);
        }
        if ($routeId) {
          $this->_get_current_methods($routeId);
        } else if (!$this->uri->segment(0) || $this->uri->segment(0) == '') {
          $this->_get_current_methods(1);
        }
      }

      // Alimenta array de links traduzidos.
      $this->db_lang_route = $this->db_lang_route[$_SESSION['user_lang']];

      // Alimenta os arrays de rotas do CodeIgniter.
      $this->db_routing = array_merge($this->db_routing, $allRoutes[$_SESSION['user_lang']]);

      if ($_SESSION['company']['sufix']) {
        $this->db_routing[rtrim($_SESSION['company']['sufix'], '/')] = 'home';

        if (!isset($this->db_routing[rtrim($_SESSION['company']['sufix'], '/') . '/(:any)'])) {
          $this->db_routing[rtrim($_SESSION['company']['sufix'], '/') . '/(:any)'] = '$1';
        }
      }

      $this->routes = $this->db_routing;
    } else {
      // Seta company se o banco não estiver desabilitado
      if ($this->db->autoinit) {
        $this->_setCompany();
      }
      // Modo tradicional de rotas do CodeIgniter atraves do config/routes.php

      // Load the routes.php file.
      if (defined('ENVIRONMENT') and is_file(APPPATH . 'config/' . ENVIRONMENT . '/routes.php')) {
        include(APPPATH . 'config/' . ENVIRONMENT . '/routes.php');
      } elseif (is_file(APPPATH . 'config/routes.php')) {
        include(APPPATH . 'config/routes.php');
      }

      $this->routes = (!isset($route) or !is_array($route)) ? array() : $route;
      unset($route);
    }

    // Set the default controller so we can display it in the event
    // the URI doesn't correlated to a valid controller.
    $this->default_controller = (!isset($this->routes['default_controller']) or $this->routes['default_controller'] == '') ? false : strtolower($this->routes['default_controller']);

    // Were there any query string segments?  If so, we'll validate them and bail out since we're done.
    if (count($segments) > 0) {
      return $this->_validate_request($segments);
    }

    // Is there a URI string? If not, the default controller specified in the "routes" file will be shown.
    if ($this->uri->uri_string == '') {
      return $this->_set_default_controller();
    }

    // Parse any custom routing that may exist
    $this->_parse_routes();

    // Re-index the segment array so that it starts with 1 rather than 0
    $this->uri->_reindex_segments();
  }

  public function get_browser_lang()
  {

    $browser_lang = strtolower(substr((isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : 'pt'), 0, 2));

    return (array_key_exists($browser_lang, $this->config->item('supported_lang'))) ? $browser_lang : $this->config->item('language_abbr');
  }

  private function _setCookie($lang = false)
  {
    global $CFG;
    $config = &$CFG->config;
    $cookie_name = 'user_lang';

    $lang = ($lang ? $lang : $this->config->item('language_abbr'));

    $_SESSION[$cookie_name] = $lang;
    $config['language_abbr'] = $lang;
    setcookie(
      $cookie_name,
      $lang,
      time() + 86400,
      '/'
    );
  }

  private function _setCompany($where = FALSE)
  {
    if (isset($where['company.slug']) && !$where['company.slug'])
      return false;

    unset($_SESSION['company']);

    // Informações Gerais da Empresa
    $this->db
      ->select('company.id')
      ->select('company.fantasy_name AS name')
      ->select('company.favicon')
      ->select('company.image')
      ->select('company.slug')
      ->select('company.languages_site')
      ->select('company.domain')
      ->select('company.status')
      ->select('company.address')
      ->select('company.number')
      ->select('company.complement')
      ->select('company.district')
      ->select('company.city')
      ->select('company.state')
      ->select('ez_country_dsc.name AS country')
      ->select('company.zipcode')
      ->select('company.phone')
      ->select('company.whatsapp')
      ->select('company.partner_whatsapp')
      ->select('company.sac_whatsapp')
      ->select('company.email')
      ->select('company.hr_email')
      ->select('company.google_tag_manager')
      ->select('company.css_file')
      ->select('company.colors')
      ->select('company.business_hours')
      ->select('lang.code AS language_main')
      ->select('x(lat_lng) AS latitude')
      ->select('y(lat_lng) AS longitude')
      ->select("ez.file as favicon")
      ->select("ez1.file as image")
      ->select("ez2.file as background_image")
      ->from('ez_company AS company')
      ->join('ez_language AS lang', 'company.language_main = lang.id', 'INNER')
      ->join('ez_file as ez', 'company.favicon = ez.id', 'LEFT')
      ->join('ez_file as ez1', 'company.image = ez1.id', 'LEFT')
      ->join('ez_file as ez2', 'company.background_image = ez2.id', 'LEFT')
      ->join('ez_country_description ez_country_dsc', 'ez_country_dsc.id_country = company.id_country AND ez_country_dsc.id_language = 1', 'LEFT')
      ->where('company.active_site', '1');
    // ->join('ez_state AS state', 'state.id = company.id_estado', 'LEFT')

    if (is_array($where)) {
      $this->db->where($where);
    } else {
      $this->db->limit(1)->order_by('id');
    }
    $query = $this->db->get();
    $company = $query->row_array();

    if (!$company)
      return false;

    $company['colors'] = json_decode($company['colors']);
    $company['language_main'] = LANG;

    // company infos
    if ($company['id'] == 2) {
      $this->db
        ->select('*')
        ->from('ez_company_info')
        ->where('ez_company_info' . '.id_company', $company['id']);

      $query = $this->db->get();
      $company['info'] = $query->result();
    }

    $_SESSION['company'] = $company;
    $_SESSION['base_url'] = $this->config->item('base_url');

    // Confere se o site possui domain proprio e não está sendo acessado dele mesmo
    if (isset($where['company.slug']) && $company['domain'] && $this->new_alias != $company['domain']) {
      header('Location: ' . $company['domain']);
    }

    $_SESSION['company']['alias_access'] = isset($where['company.domain']);
    $_SESSION['company']['slug_access'] = isset($where['company.slug']);
    $_SESSION['company']['sufix'] = $_SESSION['company']['slug_access'] ? $_SESSION['company']['slug'] . '/' : '';
    return true;
  }

  private function _getCompanyInfo()
  {
    $this->db
      ->select('ez_company_description.*')
      ->select("ez.file as favicon")
      ->select("ez1.file as image")
      ->select("ez2.file as background_image")
      ->from('ez_company_description')
      ->join('ez_company', 'ez_company.id = ez_company_description.id_company AND id_language = 1', 'LEFT')
      ->join('ez_language', 'ez_language.id = ez_company_description.id_language', 'INNER')
      ->join('ez_file as ez', 'ez_company.favicon = ez.id', 'LEFT')
      ->join('ez_file as ez1', 'ez_company.image = ez1.id', 'LEFT')
      ->join('ez_file as ez2', 'ez_company.background_image = ez2.id', 'LEFT')
      ->where('ez_company_description.id_company', $_SESSION['company']['id'])
      ->where('ez_language.code', $_SESSION['user_lang']);

    $query = $this->db->get();
    return $query->num_rows() > 0 ? $query->row_array() : array();
  }

  private function _getLangs($company)
  {
    if (!$company['languages_site'])
      return $this->config->item('supported_lang');

    $this->db->select('directory, code, id')
      ->from('ez_language')
      ->where_in('id', explode(',', $company['languages_site']))
      ->where('status', '1')
      ->where('site', '1')
      ->order_by('CASE WHEN code = "' . $company['language_main'] . '" THEN 0 ELSE code END ASC, FIELD(id, ' . $company['languages_site'] . ')', FALSE, FALSE);

    $query = $this->db->get();
    $r = $query->result();

    $langs = array();
    foreach ($r as $v) {
      $langs['codes'][$v->code] = $v->directory;
      $langs['ids'][$v->code] = $v->id;
    }

    return $langs;
  }

  public function get_routes()
  {
    return $this->db_lang_route;
  }

  public function get_current_route()
  {
    $this->config->set_item('language_abbr', $_SESSION['user_lang']);
    return $this->db_current_route;
  }

  private function _get_current_methods($routeId)
  {
    $this->db->select('ez_language.code, description.url')
      ->from('ez_route')
      ->join('ez_route_description AS description', 'description.id_route = ez_route.id', 'INNER')
      ->join('ez_language', 'ez_language.id = description.id_language', 'INNER')
      ->where(array(
        'ez_route.id' => $routeId
      ));

    $query = $this->db->get();
    $data = $query->result();
    $go = array();
    foreach ($data as $k => $v) {
      $go[$v->code] = $v->url;
    }

    $this->db_current_route = $go;
  }
}
