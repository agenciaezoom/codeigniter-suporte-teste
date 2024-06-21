<?php (defined('BASEPATH')) or exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ', 'rb');
define('FOPEN_READ_WRITE', 'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE', 'ab');
define('FOPEN_READ_WRITE_CREATE', 'a+b');
define('FOPEN_WRITE_CREATE_STRICT', 'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');
define('GMAPS_KEY', ENVIRONMENT == 'production' ? 'AIzaSyBZKaZnBJ_S7m5lJWCLlLYCsVnmg29AX9M' : 'AIzaSyBlYRX4JcJLzJQ4S8lnVZy6FdvipuszGKY');
define('GRECAPTCHA_SITE_KEY', '');
define('GRECAPTCHA_SECRET_KEY', '');

if (ENVIRONMENT == 'development') {
  if (preg_match('/\\\/', FCPATH)) { // se for Windows
    preg_match('/[^\\\\]+($)/', rtrim(FCPATH, '\\'), $match_alias);
  } else { // se for Mac
    $match_alias = explode('/', rtrim(FCPATH, '/'));
    $match_alias = array(end($match_alias));
  }
}
// switch ($_SERVER['HTTP_HOST']) {
//     case 'bontempo.cl':
//         define('LANG', 'es');
//         break;
//     case 'bontempousa.com':
//         define('LANG', 'en');
//         break;
//     default:
//         define('LANG', 'pt');
//         break;
// }
define('LANG', 'pt');

define('WEBSITE_ALIAS', serialize(
  (ENVIRONMENT == 'development' ?
    array('http://localhost/' . $match_alias[0] . '/') :
    array('http://www.sitedoprojeto.com.br/', 'http://192.168.1.250/projeto.com.br/')
  )
));

if (ENVIRONMENT == 'development') {
  if (preg_match('/mac /i', $_SERVER['HTTP_USER_AGENT']))
    define('PATH_TO_MODEL', '../../../cms/modules/');
  else
    define('PATH_TO_MODEL', '../../cms/modules/');
} else {
  define('PATH_TO_MODEL', '../../../cms/modules/');
}

/* End of file constants.php */
/* Location: ./application/config/constants.php */
