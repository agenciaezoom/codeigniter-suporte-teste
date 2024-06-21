<?php (defined('BASEPATH')) or exit('No direct script access allowed');

/**
 * Model
 *
 * @package ezoom
 * @subpackage comum
 * @category Model
 * @author Diogo Taparello
 * @copyright 2015 Ezoom
 */
class paginas_m extends MY_Model
{
  public $table = 'site_common_content';
  public $table_description = 'site_common_content_description';
  public $table_config = 'site_common_content_configuration';
  public $table_gallery = 'site_common_content_gallery';
  //public $table_gallery_description = 'site_common_content_gallery_description';
  public $table_videos = 'site_common_content_video';
  public $primary_key = 'id';
  public $foreign_key = 'id_common_content';

  public $image_fields_description = array('image', 'image_mobile');

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
      $this->db->select('COUNT(DISTINCT ' . $this->table . '.id) AS count');
    else {
      $this->db->select($this->table . '.*');

      if (!$params['id']) {
        $this->db->select($this->table_description . '.*, config.enable_edit, config.enable_delete, config.enable_gallery, config.enable_videos')
          ->join($this->table_config . ' as config', 'config.' . $this->foreign_key . ' = ' . $this->table . '.' . $this->primary_key, 'left');
      }

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

      $this->db->order_by($this->table . '.id', 'asc');
    }


    $this->db->from($this->table);

    $this->db->join($this->table_description, $this->table_description . '.' . $this->foreign_key . ' = ' . $this->table . '.' . $this->primary_key, 'left')
      ->where($this->table . '.id_company', $this->auth->data('company'))
      ->where($this->table_description . '.id_language', $this->current_lang);

    if ($params['search']) {
      if (isset($params['search']['title']) && $params['search']['title'] != '')
        $this->db->where(
          '(' .
            $this->table_description . '.title like "%' . $params['search']['title'] . '%" OR ' .
            $this->table_description . '.subtitle like "%' . $params['search']['title'] . '%" OR ' .
            $this->table_description . '.area like "%' . $params['search']['title'] . '%" OR ' .
            $this->table_description . '.subarea like "%' . $params['search']['title'] . '%" OR ' .
            $this->table_description . '.text like "%' . $params['search']['title'] . '%" OR ' .
            $this->table_description . '.link like "%' . $params['search']['title'] . '%" OR ' .
            $this->table . '.slug like "%' . $params['search']['title'] . '%")'
        );

      if (isset($params['search']['area']) && $params['search']['area'] != '')
        $this->db->where($this->table_description . '.area', $params['search']['area']);

      if (isset($params['search']['subarea']) && $params['search']['subarea'] != '')
        $this->db->where($this->table_description . '.subarea', $params['search']['subarea']);
    }

    //Faz um join left na tabela de arquivos
    if (isset($this->image_fields) && is_array($this->image_fields) && count($this->image_fields) > 0) {
      foreach ($this->image_fields as $keyImage => $image_field) {
        $this->db->select('file_' . $keyImage . '.file as ' . str_replace('id_', '', $image_field));
        $this->db->join($this->table_file . ' as file_' . $keyImage, $this->table . '.' . $image_field . ' = file_' . $keyImage . '.id', 'left', false);
      }
    }
    //Faz um join left na tabela de arquivos para as imagens da description
    if (isset($this->image_fields_description) && is_array($this->image_fields_description) && count($this->image_fields_description) > 0) {
      foreach ($this->image_fields_description as $keyImageDescription => $image_field_description) {
        $this->db->select('file_description_' . $keyImageDescription . '.file as ' . str_replace('id_', '', $image_field_description));
        $this->db->join($this->table_file . ' as file_description_' . $keyImageDescription, $this->table_description . '.' . $image_field_description . ' = file_description_' . $keyImageDescription . '.id', 'left', false);
      }
    }

    //Faz um join left na tabela de arquivos para o nome do arquivo
    $this->db->select($this->table_file . '.file as archive_name');
    $this->db->join($this->table_file, $this->table_file . '.id = ' . $this->table_description . '.archive', 'left');

    if ($params['where'] !== FALSE) {
      if (is_array($params['where']))
        $this->db->where($params['where']);
      else
        $this->db->where($params['where'], FALSE, FALSE);
    }

    $query = $this->db->get();

    if ($params['count'])
      $toReturn = (int) $query->row('count');
    else if ($params['id']) {
      $data = $query->row();
      if (!$data)
        return FALSE;

      $data->languages = array();
      $this->db->select('*')
        ->from($this->table_description)
        ->where($this->foreign_key, $data->id);

      //Faz um join left na tabela de arquivos para as imagens da description
      if (isset($this->image_fields_description) && is_array($this->image_fields_description) && count($this->image_fields_description) > 0) {
        foreach ($this->image_fields_description as $keyImageDescription => $image_field_description) {
          $this->db->select('file_description_' . $keyImageDescription . '.file as ' . str_replace('id_', '', $image_field_description));
          $this->db->join($this->table_file . ' as file_description_' . $keyImageDescription, $this->table_description . '.' . $image_field_description . ' = file_description_' . $keyImageDescription . '.id', 'left', false);
        }
      }


      $query = $this->db->get();
      $result = $query->result();

      foreach ($result as $key => $value) {
        $data->languages[$value->id_language] = $value;
      }

      if (!empty($data)) {
        $data->images = $this->get_gallery_images($data->id);
        // Vídeos
        $data->videos = $this->get_videos($data->id);
      }

      $toReturn = $data;
    } else
      $toReturn = $query->result();

    return $toReturn;
  }

  public function update_videos($videos, $id)
  {
    $this->db->where($this->foreign_key, $id)->delete($this->table_videos);
    $insertVideos = array();
    if (!empty($videos)) {
      foreach ($videos as $key => $video) {
        if (trim($video['link'])) {
          $insertVideos[] = array(
            $this->foreign_key => $id,
            'id_language' => $video['id_language'],
            'title' => !empty($video['title']) ? $video['title'] : null,
            'link' => $video['link'],
            'order_by' => $video['order_by']
          );
        }
      }
      if (!empty($insertVideos)) {
        $this->db->insert_batch($this->table_videos, $insertVideos);
      }
    }
    return count($insertVideos);
  }

  public function get_videos($id)
  {
    $this->db->select('*')
      ->from($this->table_videos)
      ->order_by('order_by')
      ->where($this->foreign_key, $id);

    $query = $this->db->get();
    return $query->result();
  }

  public function getPermission($id)
  {
    $this->db->select('config.*')
      ->from($this->table_config . ' as config')
      ->where('config.' . $this->foreign_key, $id);
    $query = $this->db->soft_delete(false)->get();

    return $query->row();
  }

  public function insert($data)
  {
    $this->db->trans_start();

    $insert = array();
    $insert['id_company'] = $this->auth->data('company');
    $insert['slug'] = $data['slug'];
    $insert['status'] = !empty($data['status']) ? 1 : 0;
    $insert['image_width'] = (!empty($data['image_width']) && is_numeric($data['image_width'])) ? $data['image_width'] : 2000;
    $insert['image_height'] = (!empty($data['image_height']) && is_numeric($data['image_height'])) ? $data['image_height'] : 757;
    $insert['image_mobile_width'] = (!empty($data['image_mobile_width']) && is_numeric($data['image_mobile_width'])) ? $data['image_mobile_width'] : 2000;
    $insert['image_mobile_height'] = (!empty($data['image_mobile_height']) && is_numeric($data['image_mobile_height'])) ? $data['image_mobile_height'] : 757;

    $permissions = array(
      'enable_title' => 'disabled',
      'enable_subtitle' => 'disabled',
      'enable_title_bold' => 'enabled',
      'enable_subtitle_bold' => 'disabled',
      'enable_area' => 'disabled',
      'enable_subarea' => 'disabled',
      'enable_slug' => 'disabled',
      'enable_text' => 'disabled',
      'enable_image' => 'disabled',
      'enable_image_dim' => 'disabled',
      'enable_image_mobile' => 'disabled',
      'enable_image_mobile_dim' => 'disabled',
      'enable_archive' => 'disabled',
      'enable_youtube_id' => 'disabled',
      'enable_link' => 'disabled',
      'enable_link_label' => 'disabled',
      'enable_status' => 'disabled',
      'enable_gallery' => 'disabled',
      'enable_videos' => 'disabled',
      'enable_edit' => 'disabled',
      'enable_delete' => 'disabled',
    );

    if ($data['area'] != '') {
      $permissions['enable_area'] = 'enabled';
    }

    if ($data['subarea'] != '') {
      $permissions['enable_subarea'] = 'enabled';
    }

    $this->db->insert($this->table, $insert);

    $id_insert = $this->db->insert_id();

    $permissions[$this->foreign_key] = $id_insert;

    $insertChild = array();

    foreach ($data['value'] as $lang => $values) {
      $image = (isset($data['file']['image'][$lang]) && strlen($data['file']['image'][$lang]) > 0) ? $data['file']['image'][$lang] : null;
      $image_mobile = (isset($data['file']['image_mobile'][$lang]) && strlen($data['file']['image_mobile'][$lang]) > 0) ? $data['file']['image_mobile'][$lang] : null;
      $archive = (isset($data['file']['archive'][$lang]) && strlen($data['file']['archive'][$lang]) > 0) ? $data['file']['archive'][$lang] : null;

      if ($values['title'] != '') {
        $permissions['enable_title'] = 'enabled';

        if (strstr($values['title'], '<strong>')) {
          $permissions['enable_title_bold'] = 'enabled';
        }
      }

      if ($values['subtitle'] != '') {
        $permissions['enable_subtitle'] = 'enabled';

        if (strstr($values['subtitle'], '<strong>')) {
          $permissions['enable_subtitle_bold'] = 'enabled';
        }
      }

      if ($values['text'] != '') {
        $permissions['enable_text'] = 'enabled';
      }

      if ($values['youtube_id'] != '') {
        $permissions['enable_youtube_id'] = 'enabled';
      }

      if ($values['link'] != '') {
        $permissions['enable_link'] = 'enabled';
      }

      if ($values['link_label'] != '') {
        $permissions['enable_link_label'] = 'enabled';
      }

      if ($image) {
        $permissions['enable_image'] = 'enabled';
      }

      if ($image_mobile) {
        $permissions['enable_image_mobile'] = 'enabled';
      }

      if ($archive) {
        $permissions['enable_archive'] = 'enabled';
      }

      $insertChild[] = array(
        'id_language' => $lang,
        $this->foreign_key => $id_insert,
        'area' => $data['area'],
        'subarea' => $data['subarea'],
        'title' => $values['title'] ? str_replace("\xc2\xa0", ' ', $values['title']) : null,
        'subtitle' => $values['subtitle'] ? str_replace("\xc2\xa0", ' ', $values['subtitle']) : null,
        'text' => $values['text'] ? $values['text'] : null,
        'link' => $values['link'] ? prep_url($values['link']) : null,
        'target' => $values['target'] ? $values['target'] : '_self',
        'link_label' => $values['link_label'] ? $values['link_label'] : null,
        'youtube_id' => $values['youtube_id'] ? $values['youtube_id'] : null,
        'image' => $image,
        'image_mobile' => $image_mobile,
        'archive' => $archive,
      );
    }

    if (isset($data['gallery']) && !empty($data['gallery'])) {
      $this->insert_gallery_images($data['gallery'], $id_insert);
      $permissions['enable_gallery'] = 'enabled';
    }

    if (isset($data['video']) && !empty($data['video'])) {
      if ($this->update_videos($data['video'], $id_insert)) {
        $permissions['enable_videos'] = 'enabled';
      }
    }

    $this->db->insert($this->table_config, $permissions);

    if (!empty($insertChild)) {
      $this->db->insert_batch($this->table_description, $insertChild);
    }

    $this->db->trans_complete();

    return $this->db->trans_status();
  }

  public function update($id, $data)
  {
    $current = $this->get(array('id' => $data['id']));

    $this->db->trans_start();
    $update = $updateGallery = array();

    $update['status'] = !empty($data['status']) ? 1 : 0;
    $update['image_width'] = (isset($data['image_width']) && !empty($data['image_width']) && is_numeric($data['image_width'])) ? $data['image_width'] : 2000;
    $update['image_height'] = (isset($data['image_height']) && !empty($data['image_height']) && is_numeric($data['image_height'])) ? $data['image_height'] : 757;
    $update['image_mobile_width'] = (isset($data['image_mobile_width']) && !empty($data['image_mobile_width']) && is_numeric($data['image_mobile_width'])) ? $data['image_mobile_width'] : 2000;
    $update['image_mobile_height'] = (isset($data['image_mobile_height']) && !empty($data['image_mobile_height']) && is_numeric($data['image_mobile_height'])) ? $data['image_mobile_height'] : 757;

    if (isset($data['slug'])) {
      $update['slug'] = $data['slug'];
    }

    if (!empty($update)) {
      $this->db->where($this->primary_key, $id);
      $this->db->update($this->table, $update);
    }
    $image = array();
    $archive = array();
    if (isset($data['value'])) {
      foreach ($data['value'] as $lang => $values) {
        $image[$lang] = (isset($data['file']['image'][$lang]) && strlen($data['file']['image'][$lang]) > 0) ? $data['file']['image'][$lang] : null;
        $image_mobile[$lang] = (isset($data['file']['image_mobile'][$lang]) && strlen($data['file']['image_mobile'][$lang]) > 0) ? $data['file']['image_mobile'][$lang] : null;
        $archive[$lang] = (isset($data['file']['archive'][$lang]) && strlen($data['file']['archive'][$lang]) > 0) ? $data['file']['archive'][$lang] : null;

        $condicoes = array(
          $this->table_description . '.' . $this->foreign_key => $data['id'],
          $this->table_description . '.id_language' => $lang
        );

        if (isset($values['title'])) {
          $insertUpdate['title'] = $values['title'] ? str_replace("\xc2\xa0", ' ', $values['title']) : null;
        }

        if (isset($values['subtitle'])) {
          $insertUpdate['subtitle'] = $values['subtitle'] ? str_replace("\xc2\xa0", ' ', $values['subtitle']) : null;
        }

        if (isset($data['area'])) {
          $insertUpdate['area'] = $data['area'];
        }

        if (isset($data['subarea'])) {
          $insertUpdate['subarea'] = $data['subarea'];
        }

        if (isset($values['text'])) {
          $insertUpdate['text'] = $values['text'] ? $values['text'] : null;
        }

        if (isset($values['youtube_id'])) {
          $insertUpdate['youtube_id'] = $values['youtube_id'] ? $values['youtube_id'] : null;
        }

        if (isset($values['link'])) {
          $insertUpdate['link'] = $values['link'] ? prep_url($values['link']) : null;
        }

        if (isset($values['target'])) {
          $insertUpdate['target'] = $values['target'];
        }

        if (isset($values['link_label'])) {
          $insertUpdate['link_label'] = $values['link_label'] ? $values['link_label'] : null;
        }

        if (isset($data['delete-file']['image'][$lang])) {
          $insertUpdate['image'] = null;
        }

        if (isset($data['delete-file']['image_mobile'][$lang])) {
          $insertUpdate['image_mobile'] = null;
        }

        if ($image[$lang]) {
          $insertUpdate['image'] = $image[$lang];
        }

        if ($image_mobile[$lang]) {
          $insertUpdate['image_mobile'] = $image_mobile[$lang];
        }

        if (isset($data['delete-file']['archive'][$lang])) {
          $insertUpdate['archive'] = null;
        }

        if ($archive[$lang]) {
          $insertUpdate['archive'] = $archive[$lang];
        }

        if (isset($insertUpdate) && !empty($insertUpdate)) {

          if (isset($current->languages[$lang])) {
            $this->db->where($condicoes);
            $this->db->update($this->table_description, $insertUpdate);
          } else {
            $insertUpdate['id_language'] = $lang;
            $insertUpdate[$this->foreign_key] = $data['id'];
            $this->db->insert($this->table_description, $insertUpdate);
          }
          $insertUpdate = array();
        }
      }
    } else {
      // Se os itens cadastrados tiverem habilitados apenas arquivos e imagens
      if (isset($data['file']['image']) || isset($data['delete-file']['image'])) {
        $each = isset($data['file']['image']) ? $data['file']['image'] : $data['delete-file']['image'];
        foreach ($each as $lang => $value) {
          $image[$lang] = isset($data['file']['image'][$lang]) && $data['file']['image'][$lang] ? $data['file']['image'][$lang] : null;

          $condicoes = array(
            $this->table_description . '.' . $this->foreign_key => $data['id'],
            $this->table_description . '.id_language' => $lang
          );

          if (isset($data['delete-file']['image'][$lang])) {
            $insertUpdate['image'] = null;
          }

          if ($image[$lang]) {
            $insertUpdate['image'] = $image[$lang];
          }

          if (isset($insertUpdate) && !empty($insertUpdate)) {
            if (isset($current->languages[$lang])) {
              $this->db->where($condicoes);
              $this->db->update($this->table_description, $insertUpdate);
            } else {
              $insertUpdate['id_language'] = $lang;
              $insertUpdate[$this->foreign_key] = $data['id'];
              $this->db->insert($this->table_description, $insertUpdate);
            }

            $insertUpdate = array();
          }
        }
      }
      if (isset($data['file']['image_mobile']) || isset($data['delete-file']['image_mobile'])) {
        $each = isset($data['file']['image_mobile']) ? $data['file']['image_mobile'] : $data['delete-file']['image_mobile'];
        foreach ($each as $lang => $value) {
          $image_mobile[$lang] = isset($data['file']['image_mobile'][$lang]) && $data['file']['image_mobile'][$lang] ? $data['file']['image_mobile'][$lang] : null;

          $condicoes = array(
            $this->table_description . '.' . $this->foreign_key => $data['id'],
            $this->table_description . '.id_language' => $lang
          );

          if (isset($data['delete-file']['image_mobile'][$lang])) {
            $insertUpdate['image_mobile'] = null;
          }

          if ($image_mobile[$lang]) {
            $insertUpdate['image_mobile'] = $image_mobile[$lang];
          }

          if (isset($insertUpdate) && !empty($insertUpdate)) {
            if (isset($current->languages[$lang])) {
              $this->db->where($condicoes);
              $this->db->update($this->table_description, $insertUpdate);
            } else {
              $insertUpdate['id_language'] = $lang;
              $insertUpdate[$this->foreign_key] = $data['id'];
              $this->db->insert($this->table_description, $insertUpdate);
            }

            $insertUpdate = array();
          }
        }
      }
      if (isset($data['file']['archive']) || isset($data['delete-file']['archive'])) {
        $each = isset($data['file']['archive']) ? $data['file']['archive'] : $data['delete-file']['archive'];
        foreach ($each as $lang => $value) {
          $archive[$lang] = isset($data['file']['archive'][$lang]) && $data['file']['archive'][$lang] ? $data['file']['archive'][$lang] : null;

          $condicoes = array(
            $this->table_description . '.' . $this->foreign_key => $data['id'],
            $this->table_description . '.id_language' => $lang
          );

          if (isset($data['delete-file']['archive'][$lang])) {
            $insertUpdate['archive'] = null;
          }

          if ($archive[$lang]) {
            $insertUpdate['archive'] = $archive[$lang];
          }

          if (isset($insertUpdate) && !empty($insertUpdate)) {
            if (isset($current->languages[$lang])) {
              $this->db->where($condicoes);
              $this->db->update($this->table_description, $insertUpdate);
            } else {
              $insertUpdate['id_language'] = $lang;
              $insertUpdate[$this->foreign_key] = $data['id'];
              $this->db->insert($this->table_description, $insertUpdate);
            }
            $insertUpdate = array();
          }
        }
      }
    }

    if (isset($data['gallery']))
      $this->insert_gallery_images($data['gallery'], $id);
    $this->update_gallery_images(
      (isset($data['oldImagesimages'])) ? $data['oldImagesimages'] : NULL
    );

    // Vídeos
    $this->db->where('id_common_content', $id);
    $this->db->delete($this->table_videos);
    if (isset($data['video']) && !empty($data['video'])) {
      $this->update_videos($data['video'], $id);
    }


    $this->db->trans_complete();

    if ($this->db->trans_status()) {
      foreach ($current->languages as $lang => $value) {
        if ((isset($image[$lang]) && $image[$lang] || isset($data['delete-file']['image'][$lang]))) {
          delete_file(dirname(FCPATH) . DS . 'userfiles' . DS . 'paginas' . DS . $value->image);
        }
        if ((isset($image_mobile[$lang]) && $image_mobile[$lang] || isset($data['delete-file']['image_mobile'][$lang]))) {
          delete_file(dirname(FCPATH) . DS . 'userfiles' . DS . 'paginas' . DS . $value->image_mobile);
        }
      }
      foreach ($current->languages as $lang => $value) {
        if ((isset($archive[$lang]) && $archive[$lang] || isset($data['delete-file']['archive'][$lang]))) {
          delete_file(dirname(FCPATH) . DS . 'userfiles' . DS . 'paginas' . DS . $value->archive);
        }
      }
    }

    return $this->db->trans_status();
  }

  public function updatePermissions($id, $data)
  {
    $this->db->trans_start();
    $this->db->select('id')
      ->from($this->table_config)
      ->where($this->foreign_key, $id);
    $query = $this->db->soft_delete(false)->get();

    unset($data['id']);

    if ($query->num_rows() > 0) {
      $sql = $this->db->where($this->foreign_key, $id)->update($this->table_config, $data);
    } else {
      $data[$this->foreign_key] = $id;
      $sql = $this->db->insert($this->table_config, $data);
    }

    $this->db->trans_complete();

    return $sql;
  }

  /**
   * @description Retorna lista de areas/subareas para filtro
   * @author Matheus Cuba [matheus.cuba@equipe.ezoom.com.br]
   * @date "2019-03-08"
   */
  public function get_areas($subareas = false)
  {
    $this->db->distinct()
      ->from($this->table_description)
      ->where($this->table_description . '.id_language', $this->current_lang)
      ->order_by('value');

    if ($subareas)
      $this->db->select($this->table_description . '.subarea as value');
    else
      $this->db->select($this->table_description . '.area as value');

    $q = $this->db->get();
    $result = $q->result();

    $result = array_map(function ($x) {
      return $x->value;
    }, $result);

    return $result;
  }

  public function delete($id = 0, $post = array())
  {
    $delete = false;

    $module = $this->get(array('id' => $id));
    if (!empty($module)) {
      $delete = $this->db->where('id', $id)->delete($this->table);
      if ($delete) {
        foreach ($module->languages as $language) {
          delete_file(dirname(FCPATH) . DS . 'userfiles' . DS . $this->module . DS . $language->image);
          delete_file(dirname(FCPATH) . DS . 'userfiles' . DS . $this->module . DS . $language->image_mobile);
          delete_file(dirname(FCPATH) . DS . 'userfiles' . DS . $this->module . DS . $language->archive);
        }

        if (isset($module->images)) {
          if (!empty($module->images)) {
            foreach ($module->images as $item) {
              delete_file(dirname(FCPATH) . DS . 'userfiles' . DS . 'paginas' . DS . $item->file);
            }
          }
        }
      }
    }
    return $delete;
  }
}
