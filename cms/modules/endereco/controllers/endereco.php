<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Endereco extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function get_cities()
    {
        $this->load->model('endereco_m');

        $this->session->keep_flashdata();

        $cities = $this->comum_m->get_cities($this->input->post('id'));
        $citiesList = array();

        foreach ($cities as $key => $city)
            $citiesList[] = array($city->id, $city->name);

        echo json_encode($citiesList);
    }

    public function get ()
    {
        $cep = $this->input->get('zipcode');
        $resultado = @file_get_contents('http://republicavirtual.com.br/web_cep.php?cep='.urlencode($cep).'&formato=query_string');
        $viacep = false;
        if (!$resultado) {
            $resultado = @file_get_contents('http://viacep.com.br/ws/'.urlencode($cep).'/querty');
            $viacep = true;
            if (!$resultado) {
                $resultado = "&resultado=0&resultado_txt=erro+ao+buscar+cep";
            }
        }
        $resultado = utf8_encode(rawurldecode($resultado));
        parse_str($resultado, $retorno);
        if ($viacep) {
            $retorno['tipo_logradouro'] = '';
            $retorno['cidade'] = $retorno['localidade'];
        }

        echo json_encode($retorno);
    }

}