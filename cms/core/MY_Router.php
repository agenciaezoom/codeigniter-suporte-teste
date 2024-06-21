<?php (defined('BASEPATH')) or exit('No direct script access allowed');

/* load the MX_Router class */
require APPPATH . "third_party/MX/Router.php";
require(BASEPATH . 'database/DB' . EXT);

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
    if (config_item('routes_db')) {
      //Se nao tiver a sessao setada, busca o idioma do navegador.
      if (!isset($_SESSION['user_lang'])) {
        $this->_setCookie($this->get_browser_lang());
      }

      $this->db = &DB();

      $this->routes = array();
      $this->db_routing = array();
      $this->db_lang_route = array();

      $this->db_routing['logout'] = "login/logout";
      $this->db_routing['simple/(:any)'] = "$1";
      $this->db_routing['esqueci-minha-senha'] = "esqueci";
      $this->db_routing['esqueci-minha-senha/(:any)'] = "esqueci/check-token/$1";

      $this->db_routing['default_controller'] = "home";
      $this->db_routing['404_override'] = 'comum/index';
      $this->db_routing['(\w{2})/(.*)'] = '$2';
      $this->db_routing['(\w{2})'] = $this->db_routing['default_controller'];

      $initialUri = $this->uri->segment(0) ? $this->uri->segment(0) : $this->db_routing['default_controller'];

      // Busca TODAS as routes do banco, de TODOS os idiomas.
      $this->db->select('ez_route.id, code, url, method, url_complement, key')
        ->from('ez_route')
        ->join('ez_route_description AS description', 'description.id_route = ez_route.id', 'INNER')
        ->join('ez_language', 'ez_language.id = description.id_language', 'INNER')
        ->where(array(
          'ez_language.status' => '1'
        ));

      $query = $this->db->get();
      $routeId = null;
      $allRoutes = array();

      if ($query->num_rows()) {
        // Percorre todas as routes, procurando a URL atual com o idioma atual.
        // Caso nao encontre com o idioma atual, seta o Session para o idioma da URL atual.
        foreach ($query->result() as $key => $data) {
          $dbUrl = str_replace('_', '-', $data->url);
          $dbUrl = explode('/', rtrim($dbUrl, '/'));
          $compareString = '';
          foreach ($dbUrl as $k => $part) {
            $compareString .= str_replace('_', '-', $k == 0 && !$this->uri->segment($k) ? $this->db_routing['default_controller'] : $this->uri->segment($k)) . '/';
          }

          $compareString = rtrim($compareString, '/');

          // Compara a URL atual com a URL da route percorrida do banco.
          // Caso a mesma URL seja igual para multiplos idiomas, pega a do idioma atual.
          // Se o idioma atual nao for uma dessas multiplas linguas, pega a primeira encontrada.
          if ($compareString === implode('/', $dbUrl)) {
            $first = (!isset($first)) ? $data->code : $first;

            if ($data->code == $_SESSION['user_lang']) {
              $goToLang = $data->code;
            }

            $routeId = $data->id;
          }

          $allRoutes[$data->code][rtrim($data->url . $data->url_complement, '/')] = $data->method;

          $this->db_lang_route[$data->code][$data->key] = $data->url;
        }

        $goToLang = isset($goToLang) ? $goToLang : (isset($first) ? $first : false);

        if ($goToLang) {
          $this->_setCookie($goToLang);
        }

        if ($routeId) {
          $this->_get_current_methods($routeId);
        }
      }

      // Alimenta array de links traduzidos.
      $this->db_lang_route = $this->db_lang_route[$_SESSION['user_lang']];

      // Alimenta os arrays de rotas do CodeIgniter.
      $this->db_routing = array_merge($this->db_routing, $allRoutes[$_SESSION['user_lang']]);
      $this->routes = $this->db_routing;
    } else {
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

    return (array_key_exists($browser_lang, config_item('supported_lang'))) ? $browser_lang : config_item('language_abbr');
  }

  private function _setCookie($lang = false)
  {
    global $CFG;
    $config = &$CFG->config;

    $cookie_name = 'user_lang';

    $lang = ($lang ? $lang : config_item('language_abbr'));

    $_SESSION[$cookie_name] = $lang;
    $config['language_abbr'] = $lang;

    setcookie(
      $cookie_name,
      $lang,
      time() + 86400,
      '/'
    );
  }

  public function get_routes()
  {
    return $this->db_lang_route;
  }

  public function get_current_route()
  {
    global $CFG;
    $config = &$CFG->config;
    $config['language_abbr'] = $_SESSION['user_lang'];

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
