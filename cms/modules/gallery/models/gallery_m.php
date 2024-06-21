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
class gallery_m extends MY_Model
{

    public function delete_image($ids = null, $gallerytable = null, $gallerypath = null)
    {
        $ids = explode(',', $ids);
        $sql = TRUE;
        $this->db->select('*')
                 ->from($gallerytable)
                 ->where_in('id', $ids);

        $query = $this->db->get();
        foreach ($query->result() as $key => $row) {
            $imagem = isset($row->id_file) ?  $row->id_file : $row->id_image;

            $this->db->select('*')
                 ->from($this->table_file)
                 ->where('id', $imagem);

            $imageResult = $this->db->get()->row();

            if (is_file(dirname(FCPATH) . DS . $gallerypath . DS . $imageResult->file)) {
                delete_file(dirname(FCPATH) . DS . $gallerypath . DS . $imageResult->file);
            }
            $sql = $this->db->delete($gallerytable, array('id' => $row->id ));
            $sql .= $this->db->delete($this->table_file, array('id' => $imagem ));
        }

        return $sql;
    }

    public function sort($item, $gallerytable = null)
    {
        $i = 1;
        $item = explode(',', $item);
        foreach ($item as $value) {
            $this->db->query("UPDATE {$gallerytable} SET order_by = {$i} WHERE id = {$value}");
            $i++;
        }
    }

    /**
     * Insere o arquivo no banco de dados e retorna o ID
     * @author Gabriel Stringari [gabriel.stringari@grupoezoom.com.br]
     * @date   2017-12-27
     * @param  [Object]     $file [objecto de arquivo com as informações]
     * @return [int]        retorna um inteiro com o ID inserido ou FALSE, no caso de falha
     */
    public function insert_file($file)
    {
        $this->db->trans_start();

        //Conteudo de file vem do UploadHandler, em libraries
        $data = array(
            'file'          => $file->name,
            'size'          => $file->size,
            'extension'     => isset($file->info->extension) && $file->info->extension ? $file->info->extension : null,
            // 'subtitle'      => isset($file->info->title) && $file->info->title ? $file->info->title : $file->info->name,
            'width'         => isset($file->info->width) && $file->info->width ? $file->info->width : null,
            'height'        => isset($file->info->height) && $file->info->height ? $file->info->height : null,
            'orientation'   => isset($file->info->orientation) && $file->info->orientation ? $file->info->orientation : null,
            'author'        => isset($file->info->author) && $file->info->author ? $file->info->author : null,
            'tags'          => isset($file->info->tags) && $file->info->tags ? $file->info->tags: null,
            'copyright'     => isset($file->info->copyright) && $file->info->copyright ? $file->info->copyright : null,
            'path'          => isset($file->info->path) && $file->info->path ? $file->info->path : null,
        );

        //$this->table_file definido no MY_Model
        $this->db->insert($this->table_file, $data);
        $id = $this->db->insert_id();

        $this->db->trans_complete();

        return $this->db->trans_status() ? $id : FALSE;

    }
}