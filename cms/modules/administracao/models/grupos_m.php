<?php if ( ! defined('BASEPATH')){exit('No direct script access allowed'); }

/**
 * Model
 *
 * @package ezoom
 * @subpackage grupos
 * @category Model
 * @author Fábio Bachi e Ralf da Rocha
 * @copyright 2014 Ezoom
 */
class grupos_m extends CI_Model {

    /**
     * Metodo construtor
     *
     */
    public function __construct() {
        parent::__construct();
    }

    public function get_group ($id){

        $this->db->select('*')
                 ->from('ez_user_group')
                 ->where('id', $id);

        $query = $this->db->get();
        return $query->row();

    }

    public function get_groups ($search, $limit, $offset){

        $this->db->select('*')
                 ->from('ez_user_group')
                 ->order_by('name', 'ASC');

        if ($offset >=0 && $limit > 0)
            $this->db->limit($limit, $offset);

        if ($this->auth->data('id') != 1)
            $this->db->where('(ez_user_group.id != 1 || ez_user_group.id = "'.$this->auth->data('groupId').'")');

        if ($search)
            $this->db->like('name', $search, 'both');

        $query = $this->db->get();
        return $query->result();

    }

    public function get_groups_total ($search){

        $this->db->select('COUNT(*) AS count')
                 ->from('ez_user_group');

        if ($this->auth->data('id') != 1)
            $this->db->where('ez_user_group.id !=', 1);

        if ($search)
            $this->db->like('name', $search, 'both');

        $query = $this->db->get();
        $query = $query->row();
        return (int) $query->count;

    }

    public function insert_group ($data){

        $this->db->insert('ez_user_group', array(
            'name' => $data['name'],
            'permissions' => isset($data['permissions']) ? json_encode($data['permissions']) : '{}',
            'status' => (isset($data['status']) ? '1' : '0'),
            'flags' => (isset($data['flags']) ? json_encode($data['flags']) : '{}')
        ));

    }

    public function update_group ($data){

        $this->db->where('id', $data['id']);
        $this->db->update('ez_user_group', array(
            'name' => $data['name'],
            'permissions' => isset($data['permissions']) ? json_encode($data['permissions']) : '{}',
            'status' => (isset($data['status']) ? '1' : '0'),
            'flags' => (isset($data['flags']) ? json_encode($data['flags']) : '{}')
        ));

    }

    public function delete_group ($id){

        $this->db->where('id', $id);
        $this->db->delete('ez_user_group');

    }

    public function delete_group_multiple ($ids){

        $ids = explode(',', $ids);
        $this->db->where_in('id', $ids);
        $this->db->delete('ez_user_group');

    }

    public function toggle_status_group ($data){

        $this->db->where('id', $data['id']);
        $this->db->update('ez_user_group', array('status' => ($data['actived'] == 'true' ? 1 : 0)));

    }

}
?>
