<?php (defined('BASEPATH')) or exit('No direct script access allowed');

/**
* Language Identifier
*
* Adds a language identifier prefix to all site_url links
*
* @copyright     Copyright (c) 2011 Wiredesignz
* @version         0.29
*
* Permission is hereby granted, free of charge, to any person obtaining a copy
* of this software and associated documentation files (the "Software"), to deal
* in the Software without restriction, including without limitation the rights
* to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
* copies of the Software, and to permit persons to whom the Software is
* furnished to do so, subject to the following conditions:
*
* The above copyright notice and this permission notice shall be included in
* all copies or substantial portions of the Software.
*
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
* IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
* FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
* AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
* LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
* OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
* THE SOFTWARE.
*/
require APPPATH."third_party/MX/Lang.php";
require APPPATH."helpers/gettext/gettext.inc.php";

class MY_Lang extends MX_Lang
{
    public $supported_lang;
    private $lang_id;

    public function __construct()
    {
        global $URI, $CFG, $IN;

        if ((!$IN && $CFG->item('routes_db')) || $IN == null) {
            die('Aten&ccedil;&atilde;o: DB Router est&aacute; gerando erro, ative e verifique o log. [log_threshold]');
        }

        $config =& $CFG->config;

        $index_page    = $config['index_page'];
        $lang_ignore   = $config['lang_ignore'];
        $routes_db     = $config['routes_db'];
        $this->supported_lang = $config['supported_lang'];
        $default_abbr  = key($config['supported_lang']);

        /* get the language abbreviation from uri */
        $uri_change = $URI->segment(1) == 'lang';
        $uri_abbr = $URI->segment($routes_db ? 2 : 1);

        /* adjust the uri string leading slash */
        $URI->uri_string = preg_replace("|^\/?|", '/', $URI->uri_string);

        if ($lang_ignore) {
            if ($uri_change || !$routes_db){
                if (isset($this->supported_lang[$uri_abbr])) {
                    $_SESSION['user_lang'] = $uri_abbr;
                    /* set the language_abbreviation cookie */
                    $IN->set_cookie('user_lang', $uri_abbr, $config['sess_expiration']);
                    $this->_set_text($uri_abbr);
                } else {
                    /* get the language_abbreviation from session or cookie */
                    $lang_abbr = isset($_SESSION['user_lang']) ? $_SESSION['user_lang'] : $IN->cookie($config['cookie_prefix'].'user_lang');
                    if ($lang_abbr)
                        $this->_set_text($lang_abbr);
                }
                if (strlen($uri_abbr) == 2) {
                    /* reset the uri identifier */
                    $index_page .= empty($index_page) ? '' : '/';

                    /* remove the invalid abbreviation */
                    $URI->uri_string = preg_replace(($routes_db ? "|^\/?lang\/$uri_abbr\/?|" : "|^\/?$uri_abbr\/?|"), '', $URI->uri_string);

                    /* redirect */
                    header('Location: '.$config['base_url'].$index_page.$URI->uri_string);
                    exit;
                }
            }else{
                /* get the language_abbreviation from cookie */
                $lang_abbr = isset($_SESSION['user_lang']) ? $_SESSION['user_lang'] : $IN->cookie($config['cookie_prefix'].'user_lang');

                $this->_set_text($lang_abbr);
            }

        } else {
            /* set the language abbreviation */
            $lang_abbr = $uri_abbr;
            $this->_set_text($lang_abbr);
        }

        /* check validity against config array */
        if (isset($this->supported_lang[$lang_abbr])) {
            /* reset uri segments and uri string */
            //$URI->_reindex_segments(array_shift($URI->segments));
            $URI->uri_string = preg_replace(($routes_db ? "|^\/?lang\/$uri_abbr\/?|" : "|^\/?$uri_abbr\/?|"), '', $URI->uri_string);

            /* set config language values to match the user language */
            $config['language'] = $this->supported_lang[$lang_abbr];
            $config['language_abbr'] = $lang_abbr;

            /* if abbreviation is not ignored */
            if (! $lang_ignore) {
                /* check and set the uri identifier */
                $index_page .= empty($index_page) ? $lang_abbr : "/$lang_abbr";

                /* reset the index_page value */
                $config['index_page'] = $index_page;
            }

            /* set the language_abbreviation cookie */
            if (!$CFG->item('routes_db')) {
                $IN->set_cookie($config['cookie_prefix'].'user_lang', $lang_abbr, $config['sess_expiration']);

                $this->_set_text($lang_abbr);
            }

        } else {
            /* if abbreviation is not ignored */
            if (! $lang_ignore) {
                /* check and set the uri identifier to the default value */
                $index_page .= empty($index_page) ? $default_abbr : "/$default_abbr";

                if (strlen($lang_abbr) == 2) {
                    /* remove invalid abbreviation */
                    $URI->uri_string = preg_replace(($routes_db ? "|^\/?lang\/$uri_abbr\/?|" : "|^\/?$uri_abbr\/?|"), '', $URI->uri_string);
                }

                /* redirect */
                header('Location: '.$config['base_url'].$index_page.$URI->uri_string);
                exit;
            }

            /* set the language_abbreviation cookie */
            if (!$CFG->item('routes_db')) {

                $goLang = isset($_SESSION['company']['language_main']) ? $_SESSION['company']['language_main'] : reset(array_keys($this->supported_lang));

                $IN->set_cookie('user_lang', $goLang, $config['sess_expiration']);
                $_SESSION['user_lang'] = $goLang;
                $this->_set_text($goLang);
                // header('Location: '.rtrim($config['base_url'],'/').$index_page.rtrim($URI->uri_string, '/'));
                // exit;

            }
        }
        log_message('debug', "Language_Identifier Class Initialized");
    }

    public function lang()
    {
        global $CFG;
        $config =& $CFG->config;

        if ($CFG->item('routes_db')) {
            return $_SESSION['user_lang'];
        } else {
            return $config['language_abbr'];
        }
    }

    public function id()
    {
        if ( isset($this->lang_id) && $this->lang_id )
            return $this->lang_id;

        $user_lang = $this->lang();
        $db =& DB();

        if ($db->autoinit){
            $db->select('id')
               ->from('ez_language')
               ->where('code', $user_lang);

            $query = $db->get();
            $this->lang_id = $query->row('id');
        } else {
            $this->lang_id = 1;
        }
        return $this->lang_id;
    }

    public function get_browser_lang()
    {
        $browser_lang = strtolower(substr((isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : 'pt'), 0, 2));
        return (array_key_exists($browser_lang, config_item('supported_lang'))) ? $browser_lang : config_item('language_abbr');
    }

    public function load($langfile = array(), $lang = '', $return = false, $add_suffix = true, $alt_path = '', $_module = '')
    {
        if (config_item('routes_db')) {
            $deft_lang = CI::$APP->config->item('language');
            $suppLang = config_item('supported_lang');
            $lang = (!isset($_SESSION['user_lang'])) ? $deft_lang : $suppLang[$_SESSION['user_lang']];

        }
        return parent::load($langfile, $lang, $return, $add_suffix, $alt_path);
    }

    private function _set_text($locale)
    {
        // gettext setup
        T_setlocale(LC_MESSAGES, $locale);
        // Set the text domain as 'messages'
        $domain = 'default';
        T_bindtextdomain($domain, APPPATH.'i18n');
        T_bind_textdomain_codeset($domain, 'UTF-8');
        T_textdomain($domain);
    }
}
