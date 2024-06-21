<?php (defined('BASEPATH')) or exit('No direct script access allowed.');

/**
 * Adiciona a funcionalidade de limpar o input->post
 */
class MY_Form_validation extends CI_Form_validation
{
    /**
     * Reescrito método is_unique para dar suporte a verificação durante a edição
     *
     * is_unique[tabela.coluna]       Valida a coluna
     * is_unique[tabela.coluna.id.1]  valida a coluna e id diferente de 1
     *
     * @author Fabio Neis [fabio@ezoom.com.br]
     * @date   2014-08-19
     * @param  [string] $str
     * @param  [string] $field
     * @return boolean
     */
    public function is_unique($str, $field)
    {
        if (substr_count($field, '.') == 3) {
            list($table,$field,$id_field,$id_val) = explode('.', $field);
            $query = $this->CI->db->limit(1)->where($field, $str)->where($id_field.' != ', $id_val)->get($table);
        } else {
            list($table, $field)=explode('.', $field);
            $query = $this->CI->db->limit(1)->get_where($table, array($field => $str));
        }

        return $query->num_rows() === 0;
    }

    /**
     * Permite a utilização de callback no mesmo controller que chamou
     * @author Fabio Neis [fabio@ezoom.com.br]
     * @date   2014-08-19
     * @param  string     $module
     * @param  string     $group
     * @return [callback]
     */
    public function run($module = '', $group = '')
    {
        (is_object($module)) and $this->CI = &$module;

        return parent::run($group);
    }

    /**
     * Limpa todos os campos
     * @author Fabio Neis [fabio@ezoom.com.br]
     * @date   2014-08-19
     * @return [none]
     */
    public function unset_field_data()
    {
        unset($this->_field_data);
    }

    /**
     * Retorna erros no formado de array
     * @author Ralf da Rocha [ralf@ezoom.com.br]
     * @date   2015-07-13
     * @param  boolean    $keys Se setado para TRUE retorna apenas as chaves onde conteve erros, se não o array completo
     * @return [array]
     */
    function error_array($keys = FALSE)
    {
        return ($keys) ? array_keys($this->_error_array) : $this->_error_array;
    }

    /**
     * Valida se o recaptcha está ok
     * @author Fábio Neis [fabio@ezoom.com.br]
     * @date   2018-01-10
     * @param  [type]     $response [description]
     * @return [type]               [description]
     */
    function validate_recaptcha($response)
    {
        $this->CI->form_validation->set_message('validate_recaptcha', 'captcha inválido');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.google.com/recaptcha/api/siteverify');
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('secret' => GRECAPTCHA_SECRET_KEY, 'response' => $response)));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close ($ch);

        if (!$response) {
            return false;
        }

        $response = json_decode($response);
        //if (!$response->success || $response->hostname != $_SERVER['HTTP_HOST']) { //Validação manual
        if (!$response->success) {
            return false;
        }

        unset($_POST['g-recaptcha-response']);
        return true;
    }
}
