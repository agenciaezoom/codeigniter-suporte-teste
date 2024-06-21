<?php if ( ! defined('BASEPATH')){exit('No direct script access allowed'); }

/**
 * Model
 *
 * @package ezoom
 * @subpackage esqueci
 * @category Model
 * @author Maurício
 * @copyright 2014 Ezoom
 */
class esqueci_m extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Gera uma nova senha aleatória
     * @param  integer $limite - Comprimento da nova senha.
     * @return integer $senha - Nova senha.
     */
    private function gerar_senha($limite = 9) {
        $vogais = 'aeuy';
        $consoantes = 'bdghjmnpqrstvz';
        $consoantes .= 'BDGHJLMNPQRSTVWXZ';
        $vogais .= "AEUY";
        $consoantes .= '23456789';
        $vogais .= '@#$%';
        $senha = '';
        $alt = time() % 2;
        for ($i = 0; $i < $limite; $i++) {
            if ($alt == 1) {
                $senha .= $consoantes[(rand() % strlen($consoantes))];
                $alt = 0;
            } else {
                $senha .= $vogais[(rand() % strlen($vogais))];
                $alt = 1;
            }
        }
        return $senha;
    }

    public function get_email($dados) {
        return $this->db
                ->select('ez_user.email, ez_user.id')
                ->from('ez_user')
                ->where('ez_user.login', $dados['username'])
                ->or_where('ez_user.email', $dados['username'])
                ->get()
                ->row();
    }

    public function set_new_password($id) {
        $this->load->library('PasswordHash');
        $nova_senha = $this->gerar_senha();
        $this->db->where('id', $id);
        if ($this->db->update('ez_user',
                array('password' => $this->passwordhash->HashPassword($nova_senha))))
            return $nova_senha;
        else
            return false;
    }
}