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

function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64url_decode($data) {
    return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
}

function download_url($url = '', $site_url = FALSE)
{
    $url = 'download/'.base64url_encode($url);
    if (!$site_url) {
        return base_url($url);
    } else {
        return site_url($url);
    }
}

function download_site_url($url = '')
{
    return str_replace('/cms/', '/', download_url($url));
}

// ------------------------------------------------------------------------
if ( ! function_exists('get_youtube_id')){
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

if ( ! function_exists('get_youtube_img')){
    function get_youtube_img($id) {
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

// ------------------------------------------------------------------------

if (!function_exists('assets')) {
    function assets($file = '', $module = false)
    {
        $CI =& get_instance();
        $module = ($module) ? CI::$APP->router->fetch_module() : 'comum';

        return $CI->config->base_url("modules/{$module}/assets/{$file}");
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

if (!function_exists('order_url')) {
    function order_url($field = null)
    {
        $CI = &get_instance();
        return site_url($CI->uri->uri_string()) . '?order=' . $field;
    }
}

if (!function_exists('order_ico')) {
    function order_ico($field = null, $order_by = array())
    {
        $ico = $order_by && $order_by['column'] == $field ? ' fa-caret-' . $order_by['order'] : '';
        return '<i class="fa'.$ico.'"></i>';
    }
}

if (! function_exists('slug')) {
    function slug($str = null, $table = null, $id = null, $separator = '-', $lowercase = true)
    {
        if(empty($str))
            return '';

        $CI = &get_instance();
        $CI->load->helper('text');
        $str = str_replace("&nbsp;", " ", $str);
        $str = str_replace("_", "-", $str);
        $str = preg_replace("/\s+/", " ", $str);
        $str = trim(strip_tags(html_entity_decode($str)));
        $str = convert_accented_characters($str);
        $str = url_title($str, $separator, $lowercase);
        if ($table) {
            $unique = false;
            $count = 1;
            $slug = $str;
            while (!$unique) {
                $CI->db->select('COUNT(*) AS total')->from($table)->where('(slug = "'.$slug.'" OR slug LIKE "%/'.$slug.'")');
                if ($id) {
                    if(is_array($id)) {
                        $CI->db->where($id);
                    } else {
                        $CI->db->where('id !=', $id);
                    }
                }
                $query = $CI->db->get();
                $query = $query->row();
                if ((int) $query->total == 0) {
                    $unique = true;
                    $str = $slug;
                } else {
                    $slug = $str.'-'.$count++;
                }
            }
        }

        return $str;
    }
}

/**
 * Create URL Title
 *
 * Takes a "title" string as input and creates a
 * human-friendly URL string with a "separator" string
 * as the word separator.
 *
 * @access  public
 * @param   string  the string
 * @param   string  the separator
 * @return  string
 */
function url_title($str, $separator = '-', $lowercase = FALSE)
{
    if ($separator == 'dash')
    {
        $separator = '-';
    }
    else if ($separator == 'underscore')
    {
        $separator = '_';
    }

    $q_separator = preg_quote($separator);

    $trans = array(
        '&.+?;'                 => '',
        '[^a-z0-9 _-]'          => '',
        '[_]'                   => $separator,
        '\s+'                   => $separator,
        '('.$q_separator.')+'   => $separator
    );

    $str = strip_tags($str);

    foreach ($trans as $key => $val)
    {
        $str = preg_replace("/".$key."/i", $val, $str);
    }

    if ($lowercase === TRUE)
    {
        $str = strtolower($str);
    }

    return trim($str, $separator);
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
    if (! preg_match('#^https?://#i', $uri)) {
        $uri = site_url($uri);
    }

    switch ($method) {
        case 'refresh': header("Refresh:0;url=".$uri);
            break;
        default: header("Location: ".$uri, true, $http_response_code);
            break;
    }
    exit;
}
