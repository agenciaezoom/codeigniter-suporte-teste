<?php (defined('BASEPATH')) or exit('No direct script access allowed');

/**
 * Model do modulo contato
 *
 * @package CMS
 * @subpackage  Contato
 * @category    Model
 * @author Diogo Taparello <diogo@ezoom.com.br>
 * @date      2016-03-31
 * @copyright Copyright  (c) 2016, Ezoom
 */
class contato_m extends MY_Model
{
    public $table = 'site_contact';
    public $primary_key = 'id';

    public function get($params = array())
    {
        $options = array(
            'search' => FALSE,
            'offset' => FALSE, // A partir de qual row retornar
            'limit'  => FALSE, // Quantidade de rows a retornar
            'count'  => FALSE, // TRUE para trazer apenas a contagem / FALSE para trazer os resultados
            'id'     => FALSE, // Trazer apenas um registro específico pelo id
            'where'  => FALSE,
            'order_by' => FALSE
        );
        $params = array_merge($options, $params);

        if ($params['count'])
            $this->db->select('COUNT(DISTINCT '.$this->table.'.id) AS count');
        else{
            $this->db->select($this->table.'.*')
                     ->select('DATE_FORMAT('.$this->table.'.created,"%d/%m/%Y") as date', false)
                     ->select('DATE_FORMAT('.$this->table.'.created,"%H:%i:%s") as hour', false);
        }

        $this->db->from($this->table)
                 ->where($this->table.'.id_company', $this->auth->data('company'));

        if ($params['id'])
            $this->db->where($this->table.'.'.$this->primary_key, $params['id']);

        if ($params['search']){
            if (isset($params['search']['title']) && $params['search']['title'] != ''){
                $this->db->like($this->table.'.name', $params['search']['title'], 'both');
                $this->db->or_like($this->table.'.email', $params['search']['title'], 'both');
                $this->db->or_like($this->table.'.message', $params['search']['title'], 'both');
            }
        }

        if (isset($params['where'])){
            if(! empty($params['where'])) {
                foreach ($params['where'] as $column => $value) {
                    $this->db->where($column, $value);
                }
            }
        }

        if ($params['limit'] !== FALSE && $params['offset'] === FALSE)
            $this->db->limit($params['limit']);
        elseif ($params['limit'] !== FALSE)
            $this->db->limit($params['limit'], $params['offset']);

        if ($params['order_by'] && is_array($params['order_by']))
            $this->db->order_by($params['order_by']['column'], $params['order_by']['order']);
        else if ($params['order_by'])
            $this->db->order_by($params['order_by']);

        $this->db->order_by($this->table.'.created', 'desc');

        $query = $this->db->get();
        if ($params['count'])
            $toReturn = (int) $query->row('count');
        else if ($params['id']){
            $data = $query->row();
            if (!$data)
                return FALSE;

            //$this->db->where($this->table.'.'.$this->primary_key, $params['id'])->update($this->table, array('status' => 'Lida'));

            $toReturn = $data;

        }else
            $toReturn = $query->result();

        return $toReturn;
    }

    public function insert_contact($contact)
    {
        $this->db->trans_start();
        $this->load->model('comum/comum_m');

        foreach ($contact as $key => $value) {
            $contact[$key] = trim($value) ? trim($value) : null;
            if ($key == 'id_city' && is_numeric($value)){
                $contact['city'] = $this->comum_m->get_city(array('id' => $value))->name;
            }
            if ($key == 'id_state' && is_numeric($value)){
                $contact['state'] = $this->comum_m->get_state(array('id' => $value))->name;
            }
        }
        unset($contact['id_city'],$contact['id_state']);

        $contact['id_company'] = $this->auth->data('company');
        $this->db->insert($this->table, $contact);
        $id = $this->db->insert_id();

        $this->db->trans_complete();

        return (is_numeric($id)) ? $id : false;
    }

    public function update_contact($id, $update_fields)
    {
        $this->db->trans_start();
        $this->db->where($this->table.'.id', $id)->update($this->table,$update_fields);
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function visualizar($id)
    {
        $fields['status'] = 'Lida';
        return $this->update_contact($id, $fields);
    }

    public function responder($id)
    {
        $fields['status'] = 'Respondida';
        return $this->update_contact($id, $fields);
    }

    public function reenviar($id)
    {
        $fields['status'] = 'Lida';
        return $this->update_contact($id, $fields);
    }

    public function export($params = null)
    {
        $query = $this->db
            ->select('
                name AS Nome,
                email AS Email,
                phone AS Telefone,
                subject AS Assunto,
                store AS Loja,
                message AS Mensagem,
                DATE_FORMAT(created, "%d/%m/%Y às %H:%i:%s") as Data
            ', FALSE)
            ->from($this->table)
            ->where('deleted', null)
            ->where($this->table.'.id_company', $this->auth->data('company'));

        if(isset($params['where']))
            foreach ($params['where'] as $column => $value)
                $this->db->where($column, $value);

        $query = $this->db->get();

        $retorno = array(
            'fields_cnt' => $query->list_fields(),
            'result'     => $query->result()
        );

        return $retorno;
    }
}