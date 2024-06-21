<?php if ( ! defined('BASEPATH')){exit('No direct script access allowed'); }

/**
 * Model
 *
 * @package ezoom
 * @subpackage login
 * @category Model
 * @author Fábio Bachi
 * @copyright 2014 Ezoom
 */
class login_m extends MY_Model {

    /**
     * Metodo construtor
     *
     */
    public function __construct() {
        parent::__construct();
    }

    public function get_user ($login, $pass){

        $this->load->library('PasswordHash');

        $this->db->select('ez_user.*, ez_user_group.name AS groupName')
                 ->from('ez_user')
                 ->join('ez_user_group', 'ez_user_group.id = ez_user.id_group', 'INNER')
                 ->where('login', $login)
                 ->or_where('email', $login);

        $query = $this->db->get();
        $user = $query->row();

        if (count($user) == 0)
            return false;

        if ($user->status == '0')
            return false;

        if (!$this->passwordhash->CheckPassword($pass, $user->password))
            return false;

        $this->db->where('id', $user->id);
        $this->db->update('ez_user', array('last_access' => date('Y-m-d H:i:s')));

        return $user;
    }

}
?>