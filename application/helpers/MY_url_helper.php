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
 * CodeIgniter Url Helpers
 *
 * @package     CodeIgniter
 * @subpackage  Url
 * @category    Helpers
 */

// ------------------------------------------------------------------------

function site_url($uri = '')
{
  $CI = &get_instance();
  $slugCompany = (isset($_SESSION['company']['sufix'])) ? $_SESSION['company']['sufix'] : '';
  return $CI->config->site_url($slugCompany . $uri) . ($slugCompany !== '' && $uri == '' ? '/' : '');
}

function base64url_encode($data)
{
  return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64url_decode($data)
{
  return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
}

function download_url($url = '', $site_url = FALSE)
{
  $url = 'download/' . base64url_encode($url);
  if (!$site_url) {
    return base_url($url);
  } else {
    return site_url($url);
  }
}

/**
 * Retorna a URL completa para utilizar a API do WhatsApp
 * @author  Gabriel Stringari de Miranda <gabriel.stringari@grupoezoom.com.br>
 * @param   $phone N�mero de telefone cadastrado no whatsapp - pode vir com formatacao
 * @param   $message Mensagem a ser passada como mensagem incial na chamada da API
 * @return  string url
 */
if (!function_exists('whatsapp_url')) {
  function whatsapp_url($phone = '', $message = '')
  {
    if ($phone === '' && $message === '')
      return false;

    $urlapi = 'https://api.whatsapp.com/send?phone=';

    if ($phone !== '') {
      $numbers = preg_replace("/[^0-9]/", "", $phone);
      if (strlen($numbers) == 11)
        $numbers = '55' . $numbers;

      $urlapi .= $numbers;
    }


    if ($message !== '') {
      $urlapi .= '&text=' . $message;
    }

    return $urlapi;
  }
}

if (!function_exists('load_svg')) {
  function load_svg($file = '', $module = FALSE, $userfiles = FALSE)
  {
    if (is_bool($module) && $module == TRUE) {
      $module = CI::$APP->router->fetch_module();
    } else if (is_bool($module) && $module == FALSE) {
      $module = 'comum';
    }

    if (!$userfiles && file_exists(APPPATH . "modules" . DS . "{$module}" . DS . "assets" . DS . "svg" . DS . "{$file}")) {
      $contents = file_get_contents(APPPATH . "modules" . DS . "{$module}" . DS . "assets" . DS . "svg" . DS . "{$file}");
    } else if (file_exists(FCPATH . "userfiles" . DS . "{$module}" . DS . "{$file}")) {
      $contents = file_get_contents(FCPATH . "userfiles" . DS . "{$module}" . DS . "{$file}");
    } else {
      $contents = FALSE;
    }

    return $contents;
  }
}

if (!function_exists('assets')) {
  function assets($file = '', $module = false)
  {
    $CI = &get_instance();
    $module = ($module) ? CI::$APP->router->fetch_module() : 'comum';

    return $CI->config->base_url(APPPATH . "modules/{$module}/assets/{$file}");
  }
}

if (!function_exists('base_img')) {
  function base_img($file = '', $module = false)
  {
    return assets("img/{$file}", $module);
  }
}

if (!function_exists('base_css')) {
  function base_css($file = '', $module = false)
  {
    if (!preg_match("/\.css/i", $file)) {
      $file .= '.css';
    }

    return assets("css/{$file}", $module);
  }
}

if (!function_exists('base_js')) {
  function base_js($file = '', $module = false)
  {
    if (!preg_match("/\.js/i", $file)) {
      $file .= '.js';
    }

    return assets("js/{$file}", $module);
  }
}

if (!function_exists('slug')) {
  function slug($str = null, $table = null, $id = null, $separator = '-', $lowercase = true)
  {
    $CI = &get_instance();
    $CI->load->helper('text');
    $str = convert_accented_characters($str);
    $str = url_title($str, $separator, $lowercase);
    if ($table) {
      $unique = false;
      $count = 1;
      $slug = $str;
      while (!$unique) {
        $CI->db->select('COUNT(*) AS total')->from($table)->where('slug', $slug);
        if ($id) {
          $CI->db->where('id !=', $id);
        }
        $query = $CI->db->get();
        $query = $query->row();
        if ((int) $query->total == 0) {
          $unique = true;
          $str = $slug;
        } else {
          $slug = $str . '-' . $count++;
        }
      }
    }

    return $str;
  }
}

/**
 * Header Redirect
 *
 * Header redirect in two flavors
 * For very fine grained control over headers, you could use the Output
 * Library's set_header() function.
 *
 * @access  public
 * @param   string  the URL
 * @param   string  the method: location or redirect
 * @return  string
 */
function redirect($uri = '', $method = 'location', $http_response_code = 302)
{
  if (!preg_match('#^https?://#i', $uri)) {
    $uri = site_url($uri);
  }

  switch ($method) {
    case 'refresh':
      header("Refresh:0;url=" . $uri);
      break;
    default:
      header("Location: " . $uri, true, $http_response_code);
      break;
  }
  exit;
}


if (!function_exists('get_youtube_id')) {
  /**
   * [get_youtube_id Retorna o id de um video do youtube.]
   * @author Ralf da Rocha [ralf@ezoom.com.br]
   * @date   2014-07-01
   * @param  [string]     $value
   * @return [string]     $id
   */
  function get_youtube_id($value)
  {
    preg_match("/(?<=(v|i)=)[a-zA-Z0-9-]+(?=&)|(?<=(?:v|i)\/)[^&\n]+|(?<=embed\/)[^\"&\n]+|(?<=(?:v|i)=)[^&\n]+|(?<=youtu.be\/)[^&\n]+/", $value, $matches);
    return isset($matches[0]) ? $matches[0] : false;
  }
}

if (!function_exists('get_youtube_img')) {
  function get_youtube_img($id)
  {
    $resolution = array(
      'maxresdefault',
      'sddefault',
      'hqdefault',
      'mqdefault',
      'default'
    );

    for ($x = 0; $x < sizeof($resolution); $x++) {
      $url = 'https://img.youtube.com/vi/' . $id . '/' . $resolution[$x] . '.jpg';
      $headers = get_headers($url);
      if ($headers[0] == 'HTTP/1.0 200 OK') {
        break;
      }
    }

    return $url;
  }
}

if (!function_exists('get_vimeo_id')) {
  /**
   * [get_vimeo_id Retorna o ID do V?eo no Vimeo]
   * @author Michael Cruz [michael@ezoom.com.br]
   * @date   2016-11-09
   * @param  [type]     $value [description]
   * @return [type]            [description]
   */
  function get_vimeo_id($value)
  {
    preg_match("/(https?:\/\/)?(www.)?(player.)?vimeo.com\/([a-z]*\/)*([0-9]{6,11})[?]?.*/", $value, $matches);
    return $matches[5];
  }
}

if (!function_exists('get_vimeo_info')) {
  /**
   * [get_vimeo_info Retorna todos os dados do V?eo do Vimeo]
   * @author Michael Cruz [michael@ezoom.com.br]
   * @date   2016-11-09
   * @param  [type]     $value [description]
   * @return [type]            [description]
   */
  function get_vimeo_info($value)
  {
    $response = current(unserialize(file_get_contents("http://vimeo.com/api/v2/video/" . get_vimeo_id($value) . ".php")));
    return $response;
  }
}

if (!function_exists('get_asset')) {
  function get_asset($filename = null, $module = 'comum', $type = 'img')
  {
    if ($filename != null) {
      return site_url("application/modules/$module/assets/$type/$filename");
    } else {
      return false;
    }
  }
}

if (!function_exists('get_image')) {
  function get_image($method = 'resize_crop', $width = '100%', $height = '100%', $quality = '100', $filename = null, $module = 'comum', $type = 'img')
  {
    if ($filename != null) {
      return site_url("image/$method?w=$width&h=$height&q=$quality&src=application/modules/$module/assets/$type/$filename");
    } else {
      return false;
    }
  }
}

if (!function_exists('get_resized_image')) {
  function get_resized_image($filename = null, $width = '100%', $height = '100%', $quality = '100', $module = 'comum', $type = 'img')
  {
    return $this->get_image('resize', $width, $height, $quality, $filename, $module, $type);
  }
}

if (!function_exists('get_resize_crop_image')) {
  function get_resize_crop_image($filename = null, $width = '100%', $height = '100%', $quality = '100', $module = 'comum', $type = 'img')
  {
    return $this->get_image('resize_crop', $width, $height, $quality, $filename, $module, $type);
  }
}
