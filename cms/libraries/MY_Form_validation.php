<?php (defined('BASEPATH')) or exit('No direct script access allowed.');

/**
 * Adiciona a funcionalidade de limpar o input->post
 */
class MY_Form_validation extends CI_Form_validation
{
    /**
     * Verifica se é um número de CNPJ válido.
     * @author Ramon Barros
     * @param $cnpj O número a ser verificado
     * @return boolean
     */
    public function cnpj($cnpj)
    {
        $cnpj = preg_replace('/\D/', '', $cnpj);

        if (strlen($cnpj) != 14) {
            return false;
        }

        if (preg_match('/^(\d{1})\1{13}$/', $cnpj)) {
            return false;
        }

        $soma = 0;
        for ($i = 0; $i < 12; $i++) {
            /** verifica qual é o multiplicador. Caso o valor do caracter seja entre 0-3, diminui o valor do caracter por 5
             * caso for entre 4-11, diminui por 13 **/
            $multiplicador = ( $i <= 3 ? 5 : 13 ) - $i;

            $soma += $cnpj{$i} * $multiplicador;
        }
        $soma = $soma % 11;

        if ($soma == 0 || $soma == 1) {
            $digitoUm=0;
        } else {
            $digitoUm = 11 - $soma;
        }

        if ((int) $digitoUm == (int) $cnpj{12}) {
            $soma = 0;

            for ($i = 0; $i < 13; $i++) {
                /** verifica qual é o multiplicador. Caso o valor do caracter seja entre 0-4, diminui o valor do caracter por 6
                 * caso for entre 4-12, diminui por 14 **/
                $multiplicador = ( $i <= 4 ? 6 : 14 ) - $i;
                $soma += $cnpj{$i} * $multiplicador;
            }
            $soma = $soma % 11;
            if ($soma == 0 || $soma == 1) {
                $digitoDois=0;
            } else {
                $digitoDois = 11 - $soma;
            }
            if ($digitoDois == $cnpj{13}) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verifica se é um número de CPF válido.
     * @author Ramon Barros
     * @param $cpf O número a ser verificado
     * @return boolean
     */
    public function cpf($cpf)
    {
        $cpf = preg_replace('/\D/', '', $cpf);

        if (strlen($cpf) != 11) {
            return false;
        }

        if (preg_match('/^(\d{1})\1{10}$/', $cpf)) {
            return false;
        }

        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += $cpf[$i] * (10-$i);
        }
        $mod = $sum % 11;
        $digit = ($mod > 1) ? (11 - $mod) : 0;

        if ($cpf[9] != $digit) {
            return false;
        }

        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += $cpf[$i] * (11-$i);
        }
        $mod = $sum % 11;
        $digit = ($mod > 1) ? (11 - $mod) : 0;

        if ($cpf[10] != $digit) {
            return false;
        }

        return true;
    }

    public function cpf_cnpj($cpf)
    {
        $validate = false;
        $doc = preg_replace('/\D/', '', $cpf);
        if (strlen($doc)==11) {
            $validate = $this->cpf($doc);
            if ($validate) {
                $this->set_message('cpf_cnpj', 'O cpf é invalido!');
            }
            return $validate;
        } elseif (strlen($doc)==14) {
            $validate = $this->cnpj($doc);
            if ($validate) {
                $this->set_message('cpf_cnpj', 'O cnpj é invalido!');
            }
            return $validate;
        }
    }

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

        $return = parent::run($group);

        if(!$return){
            $CI = &get_instance();
            Modules::run($CI->router->fetch_class().'/fallback', $CI->input->post());
        }

        return $return;
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

    public function error_array()
    {
        if (count($this->_error_array) === 0) {
            return false;
        } else {
            return $this->_error_array;
        }
    }

    public function month_check($month)
    {
        if ($month >= date('n')) {
            $this->set_message('month_check', 'O mês deve ser menor que o atual!');

            return false;
        } else {
            return true;
        }
    }

    public function year_check($year)
    {
        if ($year > date('Y')) {
            $this->set_message('year_check', 'O ano deve ser menor que o atual!');

            return false;
        } else {
            return true;
        }
    }
}
