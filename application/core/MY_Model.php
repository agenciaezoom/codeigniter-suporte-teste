<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Classe de modelagem de dados MY_Model
 * @author      Ralf da Rocha <ralf@ezoom.com.br>
 * @package     CI
 * @copyright   Ezoom
 */

$pathModel = '/../../cms/core/';

if (file_exists(__DIR__ . $pathModel . 'MY_Model.php')){
    require_once(__DIR__ . $pathModel . 'MY_Model.php');
} else {
    class MY_Model extends CI_Model {}
}
