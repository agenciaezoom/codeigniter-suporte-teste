<?php (defined('BASEPATH')) or exit('No direct script access allowed');

/**
 * Model
 *
 * @package ezoom
 * @subpackage routes
 * @category Model
 * @author Diogo Taparello
 * @copyright 2015 Ezoom
 */
class routes_m extends MY_Model
{
    public $table = 'ez_route';
    public $table_description = 'ez_route_description';
    public $primary_key = 'id';
    public $foreign_key = 'id_route';

    public function get($params = array())
    {
        $options = array(
            'search' => FALSE,
            'offset' => FALSE, // A partir de qual row retornar
            'limit' => FALSE, // Quantidade de rows a retornar
            'order_by' => FALSE, // Ordenação das colunas
            'count' => FALSE, // TRUE para trazer apenas a contagem / FALSE para trazer os resultados
            'id' => FALSE, // Trazer apenas um registro específico pelo id
            'where' => FALSE, // Array especifico de where
        );
        $params = array_merge($options, $params);

        if ($params['count'])
            $this->db->select('COUNT(DISTINCT '.$this->table.'.id) AS count');
        else{
            $this->db->select($this->table.'.*');

            if ($params['limit'] !== FALSE && $params['offset'] === FALSE)
                $this->db->limit($params['limit']);
            elseif ($params['limit'] !== FALSE)
                $this->db->limit($params['limit'], $params['offset']);

            if ($params['id'])
                $this->db->where($this->table.'.id', $params['id']);
            if ($params['order_by'] && is_array($params['order_by']))
                $this->db->order_by($params['order_by']['column'], $params['order_by']['order']);
            else if ($params['order_by'])
                $this->db->order_by($params['order_by']);

            $this->db->order_by('order_by', 'asc');
        }

        $this->db->from($this->table)
                 ->where($this->table.'.id_company', $this->auth->data('company'));

        if ($params['search']){
            if (isset($params['search']['title']) && $params['search']['title'] != '')
                $this->db->where($this->table.'.label LIKE "%'.$params['search']['title'].'%"');
        }

        if ($params['where'] !== FALSE){
            if (is_array($params['where']))
                $this->db->where($params['where']);
            else
                $this->db->where($params['where'], FALSE, FALSE);
        }

        $query = $this->db->get();
        if ($params['count'])
            $toReturn = (int) $query->row('count');
        else if ($params['id']){
            $data = $query->row();
            if (!$data)
                return FALSE;
            $data->languages = array();
            $this->db->select('*')
                     ->from($this->table_description)
                     ->where($this->foreign_key, $data->id);
            $query = $this->db->soft_delete(FALSE)->get();
            $result = $query->result();

            foreach ($result as $key => $value) {
                $data->languages[$value->id_language] = $value;
            }

            $toReturn = $data;

        }else
            $toReturn = $query->result();

        return $toReturn;
    }

    public function insert($data)
    {
        $this->db->trans_start();

        $insert = array(
            'id_company' => $this->auth->data('company'),
            'label' => $data['label'] ? $data['label'] : null,
            'url_complement' => $data['url_complement'] ? $data['url_complement'] : null,
            'key' => $data['key'],
            'method' => $data['method'],
            'order_by' => ($this->get(array('count' => TRUE)) + 1),
            'status' => isset($data['status']) ? 1 : 0
        );
        $this->db->insert($this->table, $insert);
        $id_route = $this->db->insert_id();

        foreach ($data['value'] as $lang => $values) {
            $insert_description['id_route'] = $id_route;
            $insert_description['id_language'] = $lang;
            $insert_description['url'] = $values['url'] ? rtrim($values['url'], '/').'/' : null;
            $insert_description['seo_title'] = $values['seo_title'] ? $values['seo_title'] : null;
            $insert_description['seo_description'] = $values['seo_description'] ? $values['seo_description'] : null;
            $insert_description['seo_keywords'] = $values['seo_keywords'] ? $values['seo_keywords'] : null;
            $this->db->insert($this->table_description, $insert_description);
        }
        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    public function update($id, $data)
    {
        $current = $this->get(array('id' => $id));

        $this->db->trans_start();

        $update = array(
            'label' => $data['label'] ? $data['label'] : null,
            'url_complement' => $data['url_complement'] ? $data['url_complement'] : null,
            'key' => $data['key'],
            'method' => $data['method'],
            'status' => isset($data['status']) ? 1 : 0
        );

        $this->db->where($this->primary_key, $id)->update($this->table, $update);

        foreach ($data['value'] as $lang => $values) {
            $update_description = array_map(array($this,'check_null'), $values);

            $update_description['url'] = $values['url'] ? rtrim($values['url'], '/').'/' : null;
            $update_description['seo_title'] = $values['seo_title'] ? $values['seo_title'] : null;
            $update_description['seo_description'] = $values['seo_description'] ? $values['seo_description'] : null;
            $update_description['seo_keywords'] = $values['seo_keywords'] ? $values['seo_keywords'] : null;

            $condicoes = array($this->table_description.'.'.$this->foreign_key => $id, $this->table_description.'.id_language' => $lang);

            if (isset($current->languages[$lang])){
                if ($update_description['url'] != '/'){
                    $this->db->where($condicoes)->update($this->table_description, $update_description);
                }
            }else{
                $update_description[$this->foreign_key] = $id;
                $update_description['id_language'] = $lang;
                $this->db->insert($this->table_description, $update_description);
            }
        }
        $this->db->trans_complete();

        return $this->db->trans_status();
    }

}
