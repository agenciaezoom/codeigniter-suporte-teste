<?php if ( ! defined('BASEPATH')){exit('No direct script access allowed'); }

/**
 * Model
 *
 * @package ezoom
 * @subpackage usuarios
 * @category Model
 * @author Fábio Bachi e Ralf da Rocha
 * @copyright 2014 Ezoom
 */
class usuarios_m extends MY_Model {

    public $table = 'ez_user';

    /**
     * Metodo construtor
     *
     */
    public function __construct() {
        parent::__construct();
    }

    public function get ($where)
    {
        $adjustWhere = array();
        foreach($where as $key => $val){
            $adjustWhere['ez_user.'.$key] = $val;
        }

        $this->db->select('ez_user.*')
                 ->select('(CASE WHEN ez_file.file IS NULL THEN ez_user.avatar ELSE ez_file.file END) as avatar')
                 ->from('ez_user')
                 ->join('ez_file', 'ez_file.id = ez_user.avatar', 'left')
                 ->where($adjustWhere);

        $query = $this->db->get();
        return $query->row();

    }

    public function get_users ($search = null, $limit = null, $offset = null)
    {

        $this->db->select('ez_user.*, g.name AS groupName')
                 ->from('ez_user')
                 ->join('ez_user_group AS g', 'g.id = ez_user.id_group', 'INNER')
                 ->order_by('name', 'ASC');

        if ($this->auth->data('id') != 1)
            $this->db->where('ez_user.id !=', 1);

        if ($offset >=0 && $limit > 0)
            $this->db->limit($limit, $offset);

        if ($search)
            $this->db->like('ez_user.name', $search, 'both')
                     ->or_like('email', $search, 'both')
                     ->or_like('login', $search, 'both')
                     ->or_like('g.name', $search, 'both');

        $query = $this->db->get();
        $return = array();

        foreach ($query->result() as $key => $value) {
            if( $this->auth->data('admin') == 1 || in_array($this->auth->data('company'), explode(',', $value->companies) ) )
                $return[$value->id] = $value;
        }

        return $return;

    }

    public function get_users_total ($search)
    {

        $this->db->select('COUNT(*) AS count')
                 ->from('ez_user')
                 ->join('ez_user_group AS g', 'g.id = ez_user.id_group', 'INNER');

        if ($this->auth->data('id') != 1)
            $this->db->where('ez_user.id !=', 1);

        if ($search)
            $this->db->like('ez_user.name', $search, 'both')
                     ->or_like('email', $search, 'both')
                     ->or_like('login', $search, 'both')
                     ->or_like('g.name', $search, 'both');

        $query = $this->db->get();
        $query = $query->row();
        return (int) $query->count;

    }

    public function insert ($data)
    {
        $this->db->trans_start();

        $avatar = (isset($data['file']['avatar']) && strlen($data['file']['avatar']) > 0) ? $data['file']['avatar']: null;

        $this->load->library('PasswordHash');

        $this->db->insert('ez_user', array(
            'name' => $data['name'],
            'avatar' => $avatar,
            'email' => $data['email'],
            'login' => $data['login'],
            'companies' => isset($data['companies']) ? implode(',', $data['companies']) : null,
            'phone' => isset($data['phone']) && $data['phone'] ? preg_replace('@[\D]@', '', $data['phone']) : null,
            'status' => (isset($data['status']) ? '1' : '0'),
            'admin' => (isset($data['admin']) ? '1' : '0'),
            'permissions' => isset($data['permissions']) ? json_encode($data['permissions']) : '{}',
            'id_group' => $data['id_group'],
            'id_architect' => isset($data['id_architect']) && $data['id_architect'] ? $data['id_architect'] : NULL,
            'password' => $this->passwordhash->HashPassword($data['password'])
        ));

        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    public function update ($data)
    {
        $this->db->trans_start();

        $update = array(
            'name' => $data['name'],
            'email' => $data['email'],
            'login' => $data['login'],
            'phone' => (isset($data['phone']) && $data['phone']) ? preg_replace('@[\D]@', '', $data['phone']) : null,
        );

        $avatar = (isset($data['file']['avatar']) && strlen($data['file']['avatar']) > 0) ? $data['file']['avatar']: null;
        if ($avatar || isset($data['delete-file']['avatar']) )
            $update['avatar'] = $avatar;

        $current = $this->get( array('id' => $data['id']) );

        if ($this->auth->data('id') != $data['id']){
            $update['status'] = isset($data['status']) ? '1' : '0';
            $update['id_group'] = $data['id_group'];
            $update['id_architect'] = isset($data['id_architect']) && $data['id_architect'] ? $data['id_architect'] : NULL;
        }

        //Salva Multi Empresas
        if( $this->auth->data('admin') == 1)
            $update['companies'] = (isset($data['companies']) ? implode(',', $data['companies']) : null);

        $update['admin'] = isset($data['admin']) ? '1' : '0';
        if ($this->auth->data('admin') == 1){
            $update['permissions'] = isset($data['permissions']) ? json_encode($data['permissions']) : '{}';
        }

        $this->db->where('id', $data['id'])->update('ez_user', $update);

        $this->db->trans_complete();

        // Confere se possui imagens cadastradas e deleta caso tudo ocorreu certo
        if ( $this->db->trans_status() ) {
            if ( $current->avatar && ($avatar || isset($data['delete-file']['avatar']))) {
                delete_file(dirname(FCPATH) . DS . 'cms' . DS . 'userfiles' . DS . 'avatar'  . DS . $current->avatar);
            }
        }

        return $this->db->trans_status();

    }


    public function update_user_password ($id, $password)
    {

        $this->load->library('PasswordHash');

        $this->db->where('id', $id);
        $this->db->update('ez_user', array(
            'password' => $this->passwordhash->HashPassword($password)
        ));

    }

    public function delete_user ($id)
    {

        if ($id == 1)
            return false;

        $user = $this->get_user(array('id' => $id));
        if ($user->avatar)
            $this->delete_user_avatar($id, $user->avatar);

        $this->db->where('id', $id);
        return $this->db->delete('ez_user');

    }

    public function delete_user_multiple ($ids)
    {

        $ids = explode(',', $ids);

        for ($i = 0; $i < count($ids); $i++)
            $this->delete_user($ids[$i]);

    }

    public function update_user_avatar ($id, $avatar)
    {

        $this->db->where('id', $id);
        $this->db->update('ez_user', array('avatar' => $avatar));
        $this->session->set_userdata('avatar', $avatar);

    }

    public function delete_user_avatar ($id, $avatar)
    {

        $this->db->where('id', $id);
        $this->db->update('ez_user', array('avatar' => null));
        $this->session->set_userdata('avatar', null);

        delete_file(dirname(FCPATH) . DS . 'userfiles' . DS .  'avatar'  . DS . $avatar);
    }

    public function toggle_status_user ($data)
    {

        $this->db->where('id', $data['id']);
        $this->db->update('ez_user', array('status' => ($data['actived'] == 'true' ? 1 : 0)));

    }

     /**
     * [turn_offline description]
     * @method turn_offline
     * @author Wiliam Felisiak Passaglia Castilhos [wiliam@ezoom.com.br]
     * @date   2014-10-10
     * @param  [type]       $id [description]
     * @return [type]           [description]
     */
    public function turn_offline($id)
    {
        $this->db->where('id', $id);
        $this->db->update('ez_user', array('online'=>'0'));
    }

}
