<?php (defined('BASEPATH')) or exit('No direct script access allowed');

class Setores_m extends MY_Model
{
  public $table = 'site_subject';
  public $table_description = 'site_subject_description';
  public $primary_key = 'id';
  public $foreign_key = 'id_subject';

  public function get($params = array())
  {
    $options = array(
      'search' => FALSE,
      'offset' => FALSE, // A partir de qual row retornar
      'limit' => FALSE, // Quantidade de rows a retornar
      'order_by' => FALSE, // Ordenação das colunas
      'count' => FALSE, // TRUE para trazer apenas a contagem / FALSE para trazer os resultados
      'max' => FALSE,
      'id' => FALSE, // Trazer apenas um registro específico pelo id
      'where' => FALSE, // Array especifico de where
    );
    $params = array_merge($options, $params);

    if ($params['count'])
      $this->db->select('COUNT(DISTINCT ' . $this->table . '.id) AS count');
    else if ($params['max'])
      $this->db->select('MAX(' . $this->table . '.order_by) AS order_by');
    else {

      $this->db->select($this->table . '.*, ' . $this->table_description . '.*')
        ->select('DATE_FORMAT(' . $this->table . '.created,"%d/%m/%Y") as created', FALSE);

      if ($params['limit'] !== FALSE && $params['offset'] === FALSE)
        $this->db->limit($params['limit']);
      elseif ($params['limit'] !== FALSE)
        $this->db->limit($params['limit'], $params['offset']);

      if ($params['id'])
        $this->db->where($this->table . '.id', $params['id']);
      if ($params['order_by'] && is_array($params['order_by']))
        $this->db->order_by($params['order_by']['column'], $params['order_by']['order']);
      else if ($params['order_by'])
        $this->db->order_by($params['order_by']);

      $this->db->order_by('order_by', 'asc');
    }

    $this->db->from($this->table)
      ->join($this->table_description, $this->table_description . '.' . $this->foreign_key . '=' . $this->table . '.' . $this->primary_key . ' AND ' . $this->table_description . '.id_language =' . $this->current_lang, 'left')
      ->where($this->table . '.id_company', $this->auth->data('company'));

    if ($params['search']) {
      if (isset($params['search']['title']) && $params['search']['title'] != '')
        $this->db->where($this->table_description . '.title LIKE "%' . $params['search']['title'] . '%"');
    }

    if ($params['where'] !== FALSE) {
      if (is_array($params['where']))
        $this->db->where($params['where']);
      else
        $this->db->where($params['where'], FALSE, FALSE);
    }

    $query = $this->db->get();

    if ($params['count']) {
      $data = $query->row();
      $toReturn = (int) $data->count;
    } else if ($params['max']) {
      $data = $query->row();
      $toReturn = (int) $data->order_by;
    } else if ($params['id']) {
      $data = $query->row();
      if (!$data)
        return FALSE;
      $data->languages = array();
      $this->db->select($this->table_description . '.*')
        ->from($this->table_description)
        ->where($this->foreign_key, $data->id);
      $query = $this->db->get();
      $result = $query->result();

      foreach ($result as $key => $value) {
        $data->languages[$value->id_language] = $value;
      }

      $toReturn = $data;
    } else
      $toReturn = $query->result();

    return $toReturn;
  }

  public function insert($data)
  {
    $this->db->trans_start();

    $insert = array(
      'id_company' => $this->auth->data('company'),
      'order_by' => ($this->get(array('max' => TRUE)) + 1),
      'status' => !empty($data['status']) ? 1 : 0
    );

    $this->db->insert(
      $this->table,
      $insert
    );

    $id = $this->db->insert_id();
    foreach ($data['value'] as $lang => $values) {
      $array = array($this->foreign_key => $id, 'id_language' => $lang);
      $array = array_merge($array, $values);
      $array = array_map(array($this, 'check_null'), $array);
      $this->db->insert($this->table_description, $array);
    }

    $this->db->trans_complete();

    return $this->db->trans_status();
  }

  public function update($id, $data)
  {
    $sqls = array();
    $update = array();

    // Pega dados atuais
    $current = $this->get(array('id' => $id));
    $update['status'] = !empty($data['status']) ? 1 : 0;

    $this->db->trans_start();

    if (!empty($update)) {
      $this->db->where(array($this->primary_key => $id))
        ->update($this->table, $update);
    }

    foreach ($data['value'] as $lang => $values) {
      $values = array_map(array($this, 'check_null'), $values);
      $values[$this->foreign_key] = $id;
      $values['id_language'] = $lang;
      $this->db->replace($this->table_description, $values);
    }

    $this->db->trans_complete();

    return $this->db->trans_status();
  }
}
