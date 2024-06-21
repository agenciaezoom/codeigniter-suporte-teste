<?php (defined('BASEPATH')) or exit('No direct script access allowed');

class Downloads_m extends MY_Model
{
  public $table = 'site_download';
  public $table_description = 'site_download_description';

  public $primary_key = 'id';
  public $foreign_key = 'id_download';

  public $table_product = 'site_download_product';

  public $image_fields_description = array('id_archive');

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
      'max' => FALSE,
      'site' => FALSE,
    );
    $params = array_merge($options, $params);

    if ($params['search']) {
      if (isset($params['search']['title']) && $params['search']['title'] != '')
        $this->db->where(' (
                    ' . $this->table_description . '.title like "%' . $params['search']['title'] . '%" ||
                    ' . $this->table_description . '.text like "%' . $params['search']['title'] . '%" )', null, false);
    }

    $toReturn = parent::get($params);

    if ($toReturn && $params['id']) {
      $toReturn->products = $this->_get_products($toReturn->id);
    }

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
          'id_language' => $lang
        );
        $array = array_merge($array, $values);
        $this->db->insert($this->table_description, $array);
      }
    }

    if (isset($data['products'])) {
      $this->_insert_update_product($id, $data['products']);
    }

    $this->db->trans_complete();

    return $this->db->trans_status();
  }

  public function update($id, $data)
  {
    $update = array();
    $delete_images = array();

    $update['status'] = !empty($data['status']) ? 1 : 0;
    $update['updated'] = date('Y-m-d H:i:s');

    $current = $this->get(array('id' => $id));

    $this->update_single_file($data, $update, $delete_images, $current);

    $this->db->trans_start();
    if (!empty($update)) {
      $this->db->where(array($this->primary_key => $id))
        ->update($this->table, $update);
    }

    foreach ($data['value'] as $lang => $values) {
      $values = array_map(array($this, 'check_null'), $values);
      $array = array(
        $this->foreign_key => $id,
        'id_language' => $lang
      );
      $array = array_merge($array, $values);

      $this->db->replace($this->table_description, $array);
    }

    if (isset($data['products'])) {
      $this->_insert_update_product($id, $data['products']);
    }

    $this->db->trans_complete();
    $this->delete_single_file($delete_images);

    return $this->db->trans_status();
  }

  public function get_downloads($id_product = FALSE)
  {
    $this->db
      ->select("$this->table.*, $this->table_description.*")
      ->select('ez_file.file')
      ->from($this->table_product)
      ->join($this->table, "$this->table.id = $this->table_product.id_download", 'LEFT')
      ->join($this->table_description, "$this->table_description.id_download = $this->table_product.id_download AND $this->table_description.id_language = $this->current_lang", 'LEFT')
      ->join('ez_file', 'ez_file.id = ' . $this->table_description . '.id_archive AND ' . $this->table_description . '.id_language = ' . $this->current_lang, 'LEFT');

    if ($id_product) {
      $this->db->or_where('id_product', $id_product);
    }

    $this->db->group_by('id');

    $query = $this->db->get();
    return $query->result();
  }

  protected function _insert_update_product($id_download, $products)
  {
    $this->db->where($this->foreign_key, $id_download)->delete($this->table_product);
    foreach ($products as $each) {
      $insert = [
        'id_download' => $id_download,
        'id_product' => $each,
      ];
      $this->db->insert($this->table_product, $insert);
    }
  }

  protected function _get_products($id_download)
  {
    $this->db
      ->from($this->table_product)
      ->where('id_download', $id_download);

    $query = $this->db->get();
    $array = array_map(function ($item) {
      return $item->id_product;
    }, $query->result());

    return $array;
  }
}
