<?php
define('DS', DIRECTORY_SEPARATOR);
define('APPPATH', '../');
define('APPROOT', '../');
define('BASEPATH', '../');

function show_404(){
    $lang = 'pt-br';
    include(APPPATH . 'errors/error_404.php');
}

include('image.php');

$params = $_GET;

if(isset($_SERVER['PATH_INFO']))
    $method = trim($_SERVER['PATH_INFO'], '/');
else if(isset($_SERVER['ORIG_PATH_INFO']))
    $method = trim($_SERVER['ORIG_PATH_INFO'], '/');
else {
    $method = trim($_SERVER['QUERY_STRING'], '/');
    parse_str(str_replace($_SERVER['REDIRECT_URL'].'?', '', $_SERVER['REQUEST_URI']), $params);
}

$newParams = array();
foreach ($params as $key => $each_param){
    if (preg_match('/image\//i', $key))
        $key = substr($key, -1);
    $newParams[$key] = $each_param;
}
$params = $newParams;

$params['src'] = str_replace('ezfiles', '../userfiles', $params['src']);

$image = new Image();
if (method_exists($image, $method))
{
    $image->{$method}($params);
}else{
    show_404();
}