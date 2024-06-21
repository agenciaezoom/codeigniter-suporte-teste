<?php (defined('BASEPATH')) or exit('No direct script access allowed');

/**
 * CodeIgniter MX_Model
 *
 * Metodos utilizado em todos os models no projeto atual.
 *
 * @package     CodeIgniter
 * @author      Ezoom
 * @subpackage  Model
 * @category    Model
 * @link        http://ezoom.com.br
 * @copyright  Copyright (c) 2008, Ezoom
 * @version 1.0.0
 *
 */
class MY_Model extends CI_Model
{

  /**
   * Modulo acessado
   * @var string
   */
  public $module;

  public $current_module;

  /**
   * Método acessado
   * @var string
   */
  public $method;

  /**
   * Classe acessada
   * @var string
   */
  public $class;

  /**
   * Error retornado pelo DB
   * @var string
   */
  public $error;

  /**
   * Numero do erro retornado pelo DB
   * @var integer
   */
  public $error_number;

  /**
   * Tabela do model
   * @var string
   */
  public $table;

  /**
   * Idioma atual
   * @var string
   */
  public $current_lang = null;

  public $hasCompany = TRUE;

  public $table_file = 'ez_file';
  public $table_file_description = 'ez_file_description';
  public $image_fields = false;
  public $image_fields_description = false;


  public function __construct()
  {

    // plural
    $this->load->helper('inflector');
    // delete_file
    $this->load->helper('file');
    // nome da tabela
    $this->_fetch_table();

    $this->current_lang = $this->current_lang()->id;

    if (!isset($this->lang->time_zone) || !$this->lang->time_zone) {
      $timezone = $this->db->select('lc_time_names,time_zone')
        ->from('ez_language')
        ->where('id', $this->current_lang)
        ->get()
        ->row();
      $this->lang->time_zone = $timezone->time_zone;
      $this->lang->lc_time_names = $timezone->lc_time_names;
    }
    $this->db->query("SET time_zone=" . $this->db->escape($this->lang->time_zone));
    $this->db->query("SET lc_time_names=" . $this->db->escape($this->lang->lc_time_names));

    //$this->db->query("SET lc_time_names = 'pt_BR'");
    $this->db->query("SET SESSION group_concat_max_len = 1000000");

    parent::__construct();
  }

  /**
   * Guess the table name by pluralising the model name
   */
  private function _fetch_table()
  {
    if ($this->table == null) {
      $this->table = plural(preg_replace('/(_m|_model)?$/', '', strtolower(get_class($this))));
    }
  }

  /**
   * Recupera idioma referente ao código
   * @author Ramon Barros <ramon@ezoom.com.br>
   * @date      2015-07-28
   * @copyright Copyright  (c)   2015,         Ezoom
   * @param  string $code
   * @return object
   */
  public function lang($code = null)
  {
    $this->db->select('*')
      ->from('ez_language');
    if (!empty($code)) {
      $this->db->where('code', $code);
    }

    $query = $this->db->get();

    return !empty($code) ? $query->row() : $query->result();
  }

  /**
   * Recupera o idioma atual
   * @author Ramon Barros <ramon@ezoom.com.br>
   * @date      2015-07-28
   * @copyright Copyright  (c)           2015, Ezoom
   * @return object
   */
  public function current_lang()
  {
    return $this->lang($this->lang->lang());
  }

  /**
   * Remove um registro
   * @author Diogo Taparello <diogo@ezoom.com.br>
   * @date      2016-04-01
   * @copyright Copyright  (c) 2015,         Ezoom
   * @param     integer    $id
   * @return    integer
   */
  public function delete($id = 0, $post = array())
  {
    try {
      $userfiles = dirname(FCPATH) . DS . 'userfiles' . DS . ($this->class != $this->module ? $this->current_module->slug . DS . $this->class : $this->current_module->slug);
      $module = $this->get(array('id' => $id));

      $delete_files = array();
      $delete = false;
      if (!empty($module)) {
        $order_by = false;

        $this->db->trans_start();

        $delete = $this->db->where('id', $id)->delete($this->table);

        if ($delete) {
          if (isset($module->order_by))
            $order_by = $module->order_by;

          $this->hasCompany = $this->hasCompany ? ' and id_company = ' . $this->auth->data('company') : '';

          if (isset($post['images'])) {
            $images = explode('/', $post['images']);
            foreach ($images as $key => $image) {
              if (isset($module->{"id_$image"}) && $module->{"id_$image"}) {
                $delete_files[] = $module->{"id_$image"};
              }
              if (isset($module->$image) && $module->$image != '') {
                delete_file($userfiles . DS . $module->$image);
              }
            }
          }

          if (isset($post['galleries'])) {
            $galleries = explode('/', $post['galleries']);
            foreach ($galleries as $key => $gallery) {
              if (isset($module->$gallery)) {
                foreach ($module->$gallery as $key => $value) {
                  if (isset($value->image))
                    delete_file($userfiles . DS . $value->image);
                  if (isset($value->file))
                    delete_file($userfiles . DS . $value->file);
                }
              }
            }
          }
          if (isset($module->languages)) {
            foreach ($module->languages as $lang => $value) {
              if (isset($images)) {
                foreach ($images as $key => $image) {
                  if (isset($value->{"id_$image"}) && $value->{"id_$image"}) {
                    $delete_files[] = $value->{"id_$image"};
                  }
                  if (isset($value->$image) && $value->$image != '') {
                    delete_file($userfiles . DS . $value->$image);
                  }
                }
              }
            }
          }
          if ($order_by !== false)
            $this->db->query('UPDATE ' . $this->table . ' SET order_by = (order_by - 1) WHERE order_by > ' . $this->db->escape($order_by) . $this->hasCompany);

          if (!empty($delete_files)) {
            $this->db->where_in('id', $delete_files)->delete($this->table_file);
          }
        }

        $this->db->trans_complete();
      }

      return $delete;
    } catch (Exception $e) {
      log_message('error', print_r($e, true));
    }
  }

  /**
   * Remove multiplos registros
   * @author Diogo Taparello [diogo@ezoom.com.br]
   * @date   2015-03-27
   * @param  array      $ids
   * @return boolean
   */
  public function delete_multiple($ids = array(), $post = array())
  {
    $delete = array();

    $ids = explode(',', $ids);

    $this->db->trans_start();

    foreach ($ids as $id) {
      /** exclui e armazena o resultado **/
      $delete[$id] = $this->delete($id, $post);
    }

    $this->db->trans_complete();

    return $this->db->trans_status();
  }

  /**
   * Ativa/Inativa Banners
   * Author: Diogo Taparello diogo@ezoom.com.br
   * 20.03.2015
   */
  public function sort($data)
  {
    $i = 1;
    foreach ($data['item'] as $value) {
      $this->db->trans_start();
      $this->db->query(
        'UPDATE ' . $this->table . '
                SET order_by=' . ($i + (($data['page'] - 1) * $data['show'])) . '
                WHERE id = ' . $value
      );
      $this->db->trans_complete();
      $i++;
    }
  }

  /**
   * Insere/Atualiza Vídeos
   * @author Ralf da Rocha [ralf@ezoom.com.br]
   * @date   2016-08-01
   * @param  [array]    $videos [Array com os vídeos a serem inseridos]
   * @param  [int]      $id          [ID do conteúdo]
   * @return [int]               [Quantidade de Vídeos Inseridos]
   */
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
            'link' => $video['link']
          );
        }
      }
      if (!empty($insertVideos)) {
        $this->db->insert_batch($this->table_videos, $insertVideos);
      }
    }
    return count($insertVideos);
  }

  /**
   * Retorna vídeos
   * @author Ralf da Rocha [ralf@ezoom.com.br]
   * @date   2016-08-01
   * @param  [int]      $id          [ID do conteúdo]
   * @param  boolean    $id_language [Língua a ser trazida (FALSE para trazer todas)]
   * @return [type]                  [Array de Objetos]
   */
  public function get_videos($id, $id_language = FALSE)
  {
    $this->db->select('*')
      ->from($this->table_videos)
      ->where($this->foreign_key, $id);
    if ($id_language)
      $this->db->where('id_language', $id_language);
    $query = $this->db->get();
    return $query->result();
  }

  /**
   * Retorna imagens da galeria
   * @author Gabriel Stringari [gabriel.stringari@grupoezoom.com.br]
   * @date 2017-11-01
   * @param  [int]     $id    [ID do conteúdo]
   * @param  [string]  $type  [String com nome o type, no caso de haver mais de uma galeria]
   * @return  [type] [Array de Objetos]
   */
  public function get_gallery_images($id, $type = false)
  {
    $this->db->select('gallery.id, gallery.' . $this->foreign_key . ', gallery.order_by, file.file, gallery.highlighted, file.id as file_id')
      ->from($this->table_gallery . ' as gallery')
      ->join($this->table_file . ' as file', 'gallery.id_file' . ' = file.id', 'inner')
      ->where('gallery.' . $this->foreign_key, $id)
      ->order_by('gallery.order_by', 'ASC');

    if (isset($type) && $type)
      $this->db->where('gallery.type', $type);

    if (isset($this->table_file_description)) {
      $this->db->select('file_description.subtitle')
        ->join($this->table_file_description . ' as file_description', 'gallery.id_file' . ' = file_description.id_file AND file_description.id_language = ' . $this->current_lang, 'left');
    } else {
      $this->db->select('gallery.subtitle');
    }

    $query = $this->db->get();
    $data = $query->result();

    if (isset($this->table_file_description)) {
      foreach ($data as $key => $value) {
        // $data->images[$key]->languages = array();
        $value->languages = array();
        $this->db->select('subtitle')
          ->from($this->table_file_description . ' as gallery')
          ->where('gallery.id_file', $value->file_id);
        $query = $this->db->get();
        $value->languages = $query->result();
      }
    }

    return $data;
  }

  /**
   * Grava imagens da galeria
   * @author Gabriel Stringari [gabriel.stringari@grupoezoom.com.br]
   * @date 2017-11-01
   * @param  [array]   $images                     [Array com as imagens a serem inseridos]
   * @param  [int]     $id                         [ID do conteúdo]
   * @return [int]     [Quantidade de imagens inseridos]
   */
  public function insert_gallery_images($images, $id)
  {
    $count = 0;

    foreach ($images as $key => $value) {
      if (is_array($value)) {
        $updateGallery[$this->foreign_key] = $id;
        $updateGallery['id_file'] = $value['image'];
        $updateGallery['order_by'] = !empty($value['order_by']) ? $value['order_by'] : null;
        $updateGallery['highlighted'] = isset($value['highlighted']) ? 1 : 0;

        if (isset($value['type']) && $value['type'] !== '')
          $updateGallery['type'] = $value['type'];

        if (!is_array($value['subtitle'])) {
          $insertFileDescription = array(
            $this->table_file_description . '.id_file'  => $value['image'],
            'id_language'   => $this->current_lang,
            'subtitle'      => !empty($value['subtitle']) ? $value['subtitle'] : null
          );

          $this->db->replace($this->table_file_description, $insertFileDescription);
        }

        if (!empty($updateGallery)) {
          $this->db->insert($this->table_gallery, $updateGallery);
          $count++;
          $id_gallery = $this->db->insert_id();
        }

        if (is_array($value['subtitle']) && (isset($this->table_file_description) && $this->table_file_description)) {
          $value['subtitle'] = array_map(array($this, 'check_null'), $value['subtitle']);
          foreach ($value['subtitle'] as $key => $valueSub) {
            $updateGalleryDescription = array(
              $this->table_file_description . '.id_file'  => $value['image'],
              'id_language'   => $key,
              'subtitle'      => $valueSub
            );
            $this->db->replace($this->table_file_description, $updateGalleryDescription);
            // $this->db->insert($this->table_file_description, $updateGalleryDescription);
          }
        }
      }
    }

    return $count;
  }

  /**
   * Grava imagens da galeria
   * @author Gabriel Stringari [gabriel.stringari@grupoezoom.com.br]
   * @date 2017-11-01
   * @param  [array]  [Array oldImages___]
   * @return [int]     [Quantidade de imagens atualizados]
   * update_gallery_images($data['oldImagesimages'], $data['oldImagesdetails'], $data['oldImagesimages_outro']);
   */
  public function update_gallery_images()
  {
    $args = func_get_args();
    $count = 0;

    foreach ($args as $oldImages) {
      if (isset($oldImages) && $oldImages && count($oldImages) > 0 && $oldImages !== NULL) {
        foreach ($oldImages as $key => $value) {
          $galeria = $this->db->select('id_file')->from($this->table_gallery)->where('id', $key)->get()->row();

          if (!is_array($value['subtitle'])) {
            $updateGalleryDescription = array(
              $this->table_file_description . '.id_file'  => $galeria->id_file,
              'id_language'   => $this->current_lang,
              'subtitle'      => $value['subtitle']
            );

            $this->db->replace($this->table_file_description, $updateGalleryDescription);
          }

          $updateOldGallery['highlighted'] = isset($value['highlighted']) ? 1 : 0;

          if (!empty($updateOldGallery)) {
            $this->db->where('id', $key)
              ->update($this->table_gallery, $updateOldGallery);

            $count++;
          }

          if (is_array($value['subtitle']) && (isset($this->table_file_description) && $this->table_file_description)) {
            $value['subtitle'] = array_map(array($this, 'check_null'), $value['subtitle']);


            foreach ($value['subtitle'] as $lang => $valueSub) {
              $updateGalleryDescription = array(
                $this->table_file_description . '.id_file'  => $galeria->id_file,
                'id_language'   => $lang,
                'subtitle'      => $valueSub
              );

              $this->db->replace($this->table_file_description, $updateGalleryDescription);

              // $this->db->where($this->table_file_description.'.id_file', $galeria->id_file)
              //          ->where('id_language', $lang)
              //          ->replace($this->table_gallery_description, array('subtitle' => $value));
            }
          }
        }
      }
    }

    return $count;
  }

  /**
   * Grava arquivos
   * @author Gabriel Stringari [gabriel.stringari@grupoezoom.com.br]
   * @date 2017-11-01
   * @param  [array]   $data                     [Array com as imagens a serem inseridos]
   * @param  [array]   $insert                   [Array com o conteudo a ser inserido]
   * @return [void]
   * @example
   *   $insert = array(
            'id_company' => $this->auth->data('company'),
            'order_by' => ($this->get(array('max' => TRUE)) + 1),
            'status' => !empty($data['status']) ? 1 : 0
        );
        $this->insert_single_file($data, $insert);
        $this->db->insert(
            $this->table,
            $insert
        );
   */
  public function insert_single_file(&$data, &$insert)
  {
    // Imagens
    if (isset($data['file'])) {
      foreach ($data['file'] as $name => $file) {
        if (!preg_match('/id_/', $name)) {
          $name = 'id_' . $name;
        }

        // Multilinguas
        if (is_array($file)) {
          foreach ($file as $id_language => $value) {
            $data['value'][$id_language][$name] = $value;
          }
        } else if (!empty($file)) {
          $insert[$name] = $file;
        }
      }
    }
  }

  /**
   * Atualiza arquivos
   * @author Gabriel Stringari [gabriel.stringari@grupoezoom.com.br]
   * @date 2017-11-01
   * @param  [array]   $data                     [Array com as imagens a serem inseridos]
   * @param  [array]   $update                   [Arraqy com conteúdo a ser inserdo]
   * @param  [array]   $delete_images            [Array para marcar a serem deletados]
   * @param  [array]   $current                  [Array com o conteúdo atual]
   * @return [void]
   */
  public function update_single_file(&$data, &$update, &$delete_images, $current)
  {
    // Deletar Imagens
    if (isset($data['delete-file'])) {
      foreach ($data['delete-file'] as $name => $file) {
        if (!preg_match('/id_/', $name)) {
          $name = 'id_' . $name;
        }

        // Multilinguas
        if (is_array($file)) {
          foreach ($file as $id_language => $value) {
            $data['value'][$id_language][$name] = null;
            $delete_images[] = $current->languages[$id_language]->{$name};
          }
        } else {
          $update[$name] = null;
          $delete_images[] = $current->{$name};
        }
      }
    }
    // Cadastrar Imagens
    if (isset($data['file'])) {
      foreach ($data['file'] as $name => $file) {
        if (!preg_match('/id_/', $name)) {
          $name = 'id_' . $name;
        }

        // Multilinguas
        if (is_array($file)) {
          foreach ($file as $id_language => $value) {
            $data['value'][$id_language][$name] = $value;
            if (isset($current->languages[$id_language]->{$name}) && $current->languages[$id_language]->{$name}) {
              $delete_images[] = $current->languages[$id_language]->{$name};
            }
          }
        } else {
          $update[$name] = $file;
          if ($current->{$name}) {
            $delete_images[] = $current->{$name};
          }
        }
      }
    }
  }

  /**
   * Deleta arquivos
   * @author Gabriel Stringari [gabriel.stringari@grupoezoom.com.br]
   * @date 2017-11-01
   * @param  [array]   $data                     [Array com as imagens a serem inseridos]
   * @param  [array]   $insert                   [Array com o conteudo a ser inserido]
   * @return [void]
   */
  public function delete_single_file(&$delete_images)
  {
    // Confere se possui imagens cadastradas e deleta caso tudo ocorreu certo
    if ($this->db->trans_status()) {
      if (!empty($delete_images)) {
        foreach ($delete_images as $key => $value) {
          $this->db->select('*')->from($this->table_file)->where('id', $value);
          $result = $this->db->get()->row();

          if (isset($result) && isset($result->file)) {
            //Deleta o arquivo fisicamente
            delete_file(dirname(FCPATH) . DS . 'userfiles' . DS . $this->current_module->slug  . DS . $result->file);
          }
          //Deleta o registro do arquivo
          $this->db->delete($this->table_file, array('id' => $value));
        }
      }
    }
  }

  /**
   * Busca a slug do (luke i'm (((not))) your) pai
   * @author Gabriel Stringari [gabriel.stringari@grupoezoom.com.br]
   * @date 2017-11-06
   * @param $id                        [int] ID da linha do produto
   * @param $table_parent              [string] Tabela do pai
   * @param $table_parent_description  [string] Tabela description do pai
   * @param $primary_key               [string] Primary Key do pai
   * @param $foreign_key               [string] Foreign Key da relaçao pai - pai_description
   * @return $response                 [Array] Array com as slugs no seu idioma
   */
  public function get_parent_slug($id, $table_parent, $table_parent_description, $primary_key, $foreign_key)
  {
    if (!$id || !$table_parent || !$table_parent_description || !$primary_key || !$foreign_key) {
      return;
    }

    $this->db->select($table_parent_description . '.slug,' . $table_parent_description . '.id_language')
      ->from($table_parent)
      ->join($table_parent_description, $table_parent_description . '.' . $foreign_key . ' = ' . $table_parent . '.' . $primary_key, 'left')
      ->where($table_parent . '.id_company', $this->auth->data('company'))
      ->where($table_parent . '.' . $primary_key, $id);

    $query = $this->db->get();
    $slug = $query->result();

    foreach ($slug as $value) {
      $response[$value->id_language] = $value->slug;
    }

    return $response;
  }

  /**
   * Ativa/Inativa Banners
   * Author: Diogo Taparello diogo@ezoom.com.br
   * 20.03.2015
   */
  public function toggleStatus($data)
  {
    $this->db->trans_start();

    $this->db->where('id', $data['id'])
      ->update($this->table, array($data['type'] => ($data['actived'] == 'true' ? 1 : 0)));

    $this->db->trans_complete();
    return $this->db->trans_status();
  }

  protected function check_null($value)
  {
    return $value = $value ? $value : null;
  }

  /**
   * Recupera o id do ultimo registro
   * @author Ramon Barros [ramon@ezoom.com.br]
   * @date   2015-03-30
   * @param  string  $table
   * @return integer
   */
  public function last_id($table = null)
  {
    if (is_null($table)) {
      $table = $this->table();
    }
    if ($this->db->table_exists($table)) {
      $query = $this->db->query("SHOW TABLE STATUS LIKE '{$table}';");

      return (int) $query->row()->Auto_increment;
    }

    return false;
  }

  /**
   * Recupera o nome da tabela mesmo com alias
   * table t = table
   * database.table t = table
   * database.`table` t = `table`
   * `database`.`table` `t` = `table`
   *
   * @author Ramon Barros [ramon@ezoom.com.br]
   * @date   2015-03-27
   * @param  string $table
   * @return string
   */
  public function _table_name($table = null)
  {
    if (preg_match('@[\.]?([\w-_`]+)\s{1}@', $table, $match)) {
      return end($match);
    }

    return false;
  }

  /**
   * Recupera o alias utilizado na tabela
   * <code>
   *     table.column = false
   *     table.column as c = c
   *     database.table.comun as c = c
   * </code>
   * @author Ramon Barros [ramon@ezoom.com.br]
   * @date   2015-03-27
   * @param  string $table
   * @return string
   */
  public function _table_alias($table = null)
  {
    if (preg_match('@(?:\sas)?\s([\w\W]+)$@', $table, $match)) {
      return end($match);
    } else {
      return $table;
    }

    return false;
  }

  /**
   * Recupera o nome da coluna mesmo com alias
   * <code>
   *     table.column = column
   *     table.column as c = column
   *     database.table.comun as c = column
   * </code>
   *
   * @author Ramon Barros [ramon@ezoom.com.br]
   * @date   2015-03-27
   * @param  string $column
   * @return string
   */
  public function _column_name($column = null)
  {
    if (preg_match('@[\.](\w+)@', $column, $match)) {
      return end($match);
    }

    return false;
  }

  /**
   * Tratamento das mensagens de erro do DB
   * @author Ramon Barros [ramon@ezoom.com.br]
   * @date   2015-03-31
   */
  public function set_error()
  {
    if ($this->db->_error_number()) {
      $this->error = $this->db->_error_message();
      $this->error_number = $this->db->_error_number();
      $this->db->trans_rollback();
      throw new Exception($this->error);
    }

    return false;
  }

  /**
   * Caso o metodo não exista chama um alternativo
   * <code>
   *     $this->table();
   *     $this->table_alias();
   *     $this->primary_key();
   * </code>
   * @author Ramon Barros [ramon@ezoom.com.br]
   * @date   2015-03-27
   * @param  string $name      nome do metodo chamado
   * @param  array  $arguments parametros do metodo
   * @return mixed
   */
  public function __call($method, $parameters)
  {
    if (preg_match('@^(table(?!s))@', $method)) {
      if (strpos($method, '_alias') !== false) {
        $property = str_replace('_alias', '', $method);
        if (property_exists($this, $property)) {
          return $this->_table_alias($this->$property);
        }
      }
      if (property_exists($this, $method)) {
        if (!empty($this->$method)) {
          return $this->_table_name($this->$method);
        }
      }
    } elseif (strpos($method, '_key') !== false) {
      if (property_exists($this, $method)) {
        return $this->_column_name($this->$method);
      }
    }
  }

  public function get($params = array())
  {
    $options = array(
      'search'    => FALSE,
      'offset'    => FALSE, // A partir de qual row retornar
      'limit'     => FALSE, // Quantidade de rows a retornar
      'order_by'  => FALSE, // Ordenação das colunas
      'count'     => FALSE, // TRUE para trazer apenas a contagem / FALSE para trazer os resultados
      'max'       => FALSE,
      'lang'      => FALSE,
      'id'        => FALSE, // Trazer apenas um registro específico pelo id
      'where'     => FALSE, // Array especifico de where
      'slug'      => FALSE, // Trazer apenas um registro específico pela slug
      'debug'     => FALSE, // Uilizar para debugar o codigo
      'all_companies' => FALSE
    );
    $params = array_merge($options, $params);

    if ($params['count'])
      $this->db->select('COUNT(DISTINCT ' . $this->table . '.id) AS count');
    else if ($params['max'])
      $this->db->select('MAX(' . $this->table . '.order_by) AS order_by');
    else {
      $this->db->select($this->table . '.*')
        ->select('DATE_FORMAT(' . $this->table . '.created,"%d/%m/%Y") as created', FALSE);

      if (isset($this->table_description) && $this->table_description) {
        $this->db->select($this->table_description . '.*');
      }

      if ($params['limit'] !== FALSE && $params['offset'] === FALSE)
        $this->db->limit($params['limit']);
      elseif ($params['limit'] !== FALSE)
        $this->db->limit($params['limit'], $params['offset']);

      if ($params['id'])
        $this->db->where($this->table . '.id', $params['id']);
      if ($params['slug'])
        $this->db->where($this->table_description . '.slug', $params['slug']);
      if ($params['order_by'] && is_array($params['order_by']))
        $this->db->order_by($params['order_by']['column'], $params['order_by']['order']);
      else if ($params['order_by'])
        $this->db->order_by($params['order_by']);

      $this->db->order_by('order_by', 'asc');
    }

    $this->db->from($this->table);

    if (!$params['all_companies']) {
      $this->db->where($this->table . '.id_company', $this->auth->data('company'));
    }

    if (isset($this->table_description) && $this->table_description) {

      $lang = ($params['lang']) ? $params['lang'] : $this->current_lang;

      $this->db->join($this->table_description, $this->table_description . '.' . $this->foreign_key . ' = ' . $this->table . '.' . $this->primary_key . ' AND ' . $this->table_description . '.id_language =' . $lang, 'left');
    }

    if (!$params['count'] && !$params['max']) {
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
    }

    if ($params['where'] !== FALSE) {
      if (is_array($params['where']))
        $this->db->where($params['where']);
      else
        $this->db->where($params['where'], FALSE, FALSE);
    }

    $query = $this->db->get();

    if ($params['debug']) {
      echo '<pre>';
      die(var_dump($this->db->last_query()));
    }

    if ($params['count']) {
      $data = $query->row();
      $toReturn = (int) $data->count;
    } else if ($params['max']) {
      $data = $query->row();
      $toReturn = (int) $data->order_by;
    } else if ($params['id'] || $params['slug']) {
      $data = $query->row();
      if (!$data)
        return FALSE;

      /*$data->languages = array();
            $this->db->select('*')
                     ->from($this->table_description)
                     ->where($this->foreign_key, $data->id);
            $query = $this->db->get();
            $result = $query->result();

            foreach ($result as $key => $value) {
                $data->languages[$value->id_language] = $value;
            }*/

      if (isset($this->table_description) && $this->table_description) {
        $this->_get_description($data);
      }

      if (isset($this->table_gallery))
        $data->images = $this->get_gallery_images($data->id, 'images');

      $toReturn = $data;
    } else
      $toReturn = $query->result();

    if (is_array($toReturn)) {
      foreach ($toReturn as $key => $value) {
        if (isset($this->table_gallery)) {
          $value->images = $this->get_gallery_images($value->id, 'images');
        }
      }
    }

    return $toReturn;
  }

  private function _get_description(&$data)
  {
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
  }
}
