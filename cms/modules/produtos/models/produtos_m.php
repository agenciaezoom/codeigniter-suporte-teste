<?php (defined('BASEPATH')) or exit('No direct script access allowed');

class produtos_m extends MY_Model
{
  public $table = 'site_product';
  public $table_description = 'site_product_description';

  public $table_product_attribute = 'site_product_attribute';
  public $table_product_field = 'site_product_field';

  public $table_attribute = 'site_attribute';
  public $table_attribute_description = 'site_attribute_description';

  public $primary_key = 'id';
  public $foreign_key = 'id_product';

  public $image_fields_description = array('id_image', 'id_background_image');

  public function get($params = array())
  {
    $options = array(
      'search' => FALSE,
      'offset' => FALSE, // A partir de qual row retornar
      'limit' => FALSE, // Quantidade de rows a retornar
      'order_by' => FALSE, // Ordenação das colunas
      'count' => FALSE, // TRUE para trazer apenas a contagem / FALSE para trazer os resultados
      'id' => FALSE, // Trazer apenas um registro específico pelo id
      'slug' => FALSE, // Trazer apenas um registro específico pelo id
      'where' => FALSE, // Array especifico de where
      'site' => FALSE,
      'filters' => [],
    );
    $params = array_merge($options, $params);

    if ($params['search']) {
      if (isset($params['search']['title']) && $params['search']['title'] != '')
        $this->db->where(' (
                    ' . $this->table_description . '.title like "%' . $params['search']['title'] . '%" ||
                    ' . $this->table_description . '.ref like "%' . $params['search']['title'] . '%" )', null, false);
    }

    $toReturn = parent::get($params);

    if ($toReturn && ($params['id'] || $params['slug'])) {
      $toReturn->infos = $this->get_fields($toReturn->id, $this->current_lang);

      if ($params['slug']) {
        $toReturn->attributes = $this->_get_attributes($toReturn->id);
      } else {
        $toReturn->attributes = $this->_get_attributes($toReturn->id, TRUE);
      }
    }

    return $toReturn;
  }

  public function insert($data, $id = FALSE)
  {
    $this->db->trans_start();

    $insert = array(
      'id_company' => $this->auth->data('company'),
      'order_by' => ($this->get(array('max' => TRUE)) + 1),
      'status' => !empty($data['status']) ? 1 : 0,
      'type' => $data['type'],
    );

    $this->insert_single_file($data, $insert);

    $this->db->insert(
      $this->table,
      $insert
    );

    $id = $this->db->insert_id();

    if (!empty($data['value'])) {
      foreach ($data['value'] as $lang => $values) {
        $values = array_map(array($this, 'check_null'), $values);

        $array = array(
          $this->foreign_key => $id,
          'id_language' => $lang,
          'slug' => slug($values['title'], $this->table_description),
        );
        $array = array_merge($array, $values);
        $this->db->insert($this->table_description, $array);
      }
    }

    if (isset($data['fields']) && !empty($data['fields'])) {
      $this->update_fields($data['fields'], $id);
    }

    if (isset($data['attributes']) && !empty($data['attributes'])) {
      $this->update_attributes($data['attributes'], $id);
    }

    $this->db->trans_complete();

    return $this->db->trans_status() ? $id : FALSE;
  }

  public function update($id, $data)
  {
    $update = array();
    $update['status'] = !empty($data['status']) ? 1 : 0;
    $update['type'] = $data['type'];

    $current = $this->get(array('id' => $id));

    $this->update_single_file($data, $update, $delete_images, $current);

    $this->db->trans_start();
    if (!empty($update)) {
      $this->db
        ->where(array($this->primary_key => $id))
        ->update($this->table, $update);
    }

    foreach ($data['value'] as $lang => $values) {
      $values = array_map(array($this, 'check_null'), $values);
      $array = array(
        $this->foreign_key => $id,
        'id_language' => $lang,
        'slug' => slug($values['title'], $this->table_description, ['id_product !=' => $id]),
      );
      $array = array_merge($array, $values);

      $this->db->replace($this->table_description, $array);
    }

    $this->db->where($this->foreign_key, $id)->delete($this->table_product_field);
    if (isset($data['fields']) && !empty($data['fields'])) {
      $this->update_fields($data['fields'], $id);
    }

    $this->db->where($this->foreign_key, $id)->delete($this->table_product_attribute);
    if (isset($data['attributes']) && !empty($data['attributes'])) {
      $this->update_attributes($data['attributes'], $id);
    }

    $this->db->trans_complete();

    $this->delete_single_file($delete_images);

    return $this->db->trans_status();
  }

  public function update_fields($fields, $id)
  {
    $this->db->where($this->foreign_key, $id)->delete($this->table_product_field);
    $insert = [];
    if (!empty($fields)) {
      foreach ($fields as $key => $field) {
        if (!empty($field['field'])) {
          $insert[] = array(
            $this->foreign_key => $id,
            'id_language' => $field['id_language'] ?? 1,
            'field' => $field['field'],
            'value' => !empty($field['value']) ? $field['value'] : null,
          );
        }
      }
      if (!empty($insert)) {
        $this->db->insert_batch($this->table_product_field, $insert);
      }
    }
    return count($insert);
  }

  public function get_fields($id, $id_language = FALSE)
  {
    $this->db
      ->select('*')
      ->from($this->table_product_field)
      ->where($this->foreign_key, $id);

    if ($id_language) {
      $this->db->where('id_language', $id_language);
    }

    $query = $this->db->get();
    return $query->result();
  }

  public function update_attributes($attributes, $id)
  {
    $this->db->where($this->foreign_key, $id)->delete($this->table_product_attribute);

    $insert = [];
    if (!empty($attributes)) {
      foreach ($attributes as $key => $each) {
        $insert[] = [
          $this->foreign_key => $id,
          'id_attribute' => $each
        ];
      }
      if (!empty($insert)) {
        $this->db->insert_batch($this->table_product_attribute, $insert);
      }
    }

    return count($insert);
  }

  private function _get_attributes($id, $only_ids = FALSE)
  {
    $this->db
      ->select("$this->table_attribute.*, $this->table_attribute_description.*")
      ->select('ez_file.file as image')
      ->from($this->table_product_attribute)
      ->join($this->table_attribute, "$this->table_attribute.id = $this->table_product_attribute.id_attribute", 'LEFT')
      ->join($this->table_attribute_description, "$this->table_attribute_description.id_attribute = $this->table_product_attribute.id_attribute AND $this->table_attribute_description.id_language = $this->current_lang", 'LEFT')
      ->join('ez_file', "ez_file.id = $this->table_attribute.id_image", 'LEFT')
      ->where($this->foreign_key, $id);

    $query = $this->db->get();
    $result = $query->result();

    if ($only_ids) {
      return array_column($result, 'id_attribute');
    }

    return $result;
  }

  public function getTypes($params = [])
  {
    $options = array(
      'search' => FALSE,
      'where' => FALSE,
    );
    $params = array_merge($options, $params);

    $this->db
      ->select('DISTINCT type', false, null)
      ->from("$this->table")
      ->where('type IS NOT NULL', null, false);

    if ($params['where']) {
      $this->db->where($params['where']);
    }

    $query = $this->db->get();
    $types = $query->result();

    $types = array_map(function ($x) {
      return $x->type;
    }, $types);

    return $types;
  }
}
