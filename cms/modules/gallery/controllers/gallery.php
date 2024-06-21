<?php (defined('BASEPATH')) or exit('No direct script access allowed');

class Gallery extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('gallery_m');

        // Mantem sessão flashdata
        //$this->session->keep_filter(array('Galeria'));
    }

    private function valid_array($param = null){
        if (is_array($param) ) {
            $chave = key($param);
            if( is_array($param[$chave]) )
                $new_param = $this->valid_array($param[$chave]);
            else
                $new_param = $param[$chave];
        } else
            $new_param = $param;

         return $new_param;
    }

    public function upload_chunk($data = array())
    {
        error_reporting(E_ALL | E_STRICT);

        extract($data);

        if(!isset($gallerypath)){
            $gallerypath = '/userfiles/imagens/';
        }

        $folder = dirname(FCPATH) . DS . $gallerypath;
        $this->_check_folder($folder);
        $params = array(
            'param_name' => $galleryname,
            'upload_dir' => rtrim($folder, DS) . DS,
            'image_versions' => array()
        );

        $this->load->library('UploadHandler', $params);
    }

    public function upload($name = null)
    {
        $response = array();
        $data = $this->input->post();


        set_time_limit(0);

        extract($data);

        if (isset($gallerytype)) {
            $name = $gallerytype;
        }

        if (!isset($gallerypath)) {
            $gallerypath = 'userfiles/imagens/';
        }
        // Cria o diretório (caso não exista) e já insere o .gitignore
        $folder = dirname(FCPATH) . DS . $gallerypath;
        if (!is_dir($folder)) {
            @mkdir($folder, 0755);
            if(is_dir($folder)){
                $content = "*".PHP_EOL."!*/".PHP_EOL."!.gitignore";
                $fp = fopen($folder . "/.gitignore","wb");
                fwrite($fp,$content);
                fclose($fp);
            }
        }

        if (isset($data['gallerychunk']) && $data['gallerychunk'] == 'true')
            return $this->upload_chunk($data);

        $this->load->helper('file');
        $this->load->library('WideImage');

        $file = (!is_null($name) && isset($_FILES[$name])) ? $_FILES[$name] : $_FILES['file'];

        $file['name'] = $this->valid_array($file['name']);
        $file['type'] = $this->valid_array($file['type']);
        $file['tmp_name'] = $this->valid_array($file['tmp_name']);
        $file['error'] = $this->valid_array($file['error']);
        $file['size'] = $this->valid_array($file['size']);

        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                $file = isset($file) ? $file : false;
                break;
            case UPLOAD_ERR_INI_SIZE:
                $response['message'] = 'O arquivo no upload é maior do que o limite definido !';
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $response['message'] = 'O arquivo no upload é maior do que o limite definido !';
                break;
            case UPLOAD_ERR_PARTIAL:
                $response['message'] = 'o upload não foi completado com sucesso!';
                break;
            case UPLOAD_ERR_NO_FILE:
                $response['message'] = 'o upload não foi completado com sucesso!';
                break;
        }

        if ($file) {
            $response['status'] = true;
            $response['classe'] = 'success';

            $originalName = $file['name'];
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $file_name = pathinfo($file['name'], PATHINFO_FILENAME);
            $hash = md5(date('U') . uniqid(rand(), true) . $file['name']);

            $i = 1;
            $newName = $name = slug($file_name);
            $newPath = dirname(FCPATH) . DS . $gallerypath . DS . $newName  . '.' . strtolower($extension);
            while (is_file($newPath)) {
                $name = $newName . '-' . $i;
                $newPath = dirname(FCPATH) . DS . $gallerypath . DS . $name . '.' . strtolower($extension);
                $i++;
            }
            $newName = $name . '.' . strtolower($extension);

            if (!move_uploaded_file($file["tmp_name"], $newPath)) {
                $response['status'] = false;
                $response['classe'] = 'error';
            } else {
                // if (in_array(strtolower($extension), array('jpg', 'png', 'jpeg'))) {
                if(isset($data['resize'])){
                    $width = !empty($data['width']) && (int)$data['width'] > 1600 ? $data['width'] : 1600;
                    $height = !empty($data['height']) && (int)$data['height'] > 1200 ? $data['width'] : 1200;
                    $corte = !empty($data['fit']) ? $data['fit'] : 'inside';

                    list($realWidth, $realHeight, $t, $a) = getimagesize( $newPath );

                    if($width < $realWidth || $height < $realHeight)
                        $this->wideimage->load($newPath)->resize($width, $height, $corte)->saveToFile($newPath);
                }

                $file = new \stdClass();
                $file->name = $newName;

                $size = filesize($newPath);
                if ($size < 0) {
                    $size += 2.0 * (PHP_INT_MAX + 1);
                }
                $file->size = $size;

                $size = getimagesize($newPath, $info);
                list($width, $height) = $size;
                $ext = pathinfo($newPath, PATHINFO_EXTENSION);
                $file->info = array(
                    'width'         => $width,
                    'height'        => $height,
                    'orientation'   => $width >= $height ? 'h' : 'v',
                    'extension'     => $ext,
                    'name'          => rtrim($name, '.'.$ext)
                );

                if(isset($info['APP13'])) {
                    $iptc = iptcparse($info['APP13']);

                    $iptcHeaderArray = array(
                        '2#005'=>'title',
                        // '2#010'=>'Urgency',
                        '2#015'=>'category',
                        '2#025'=>'tags',
                        // '2#020'=>'Subcategories',
                        // '2#040'=>'SpecialInstructions',
                        // '2#055'=>'CreationDate',
                        '2#080'=>'author',
                        // '2#085'=>'AuthorTitle',
                        // '2#090'=>'City',
                        // '2#095'=>'State',
                        // '2#101'=>'Country',
                        // '2#105'=>'Headline',
                        // '2#110'=>'Source',
                        // '2#115'=>'PhotoSource',
                        '2#116'=>'copyright',
                        '2#120'=>'caption',
                        // '2#122'=>'captionWriter'
                    );

                    if (isset($iptc) && is_array($iptc)){
                        $head = array();
                        foreach ($iptc as $key => $value) {
                            if (isset($iptcHeaderArray[$key])){
                                $head[$iptcHeaderArray[$key]] = implode(';', $value);
                            }
                        }
                        $file->info = array_merge($file->info, $head);
                    }
                }
                $file->info = (object) $file->info;
                $file->info->path = $gallerypath;
                $id_file = $this->gallery_m->insert_file($file);

                $response['message'] = 'Imagem enviada com sucesso!';
                // } else {
                //     $response['message'] = 'Arquivo enviado com sucesso!';
                // }
            }

            if(!isset($id_file)){
                $response['status'] = false;
                $response['classe'] = 'error';
            }else{
                $response['image'] = $newName;
                $response['file_id'] = $id_file;
            }
        } else {
            $response['status'] = false;
            $response['classe'] = 'error';
        }
        echo json_encode($response);
    }

    public function delete_image()
    {
        $ids = $this->input->post('ids');
        $gallerytable = $this->input->post('gallerytable');
        $gallerypath = $this->input->post('gallerypath');

        if ($ids) {
            if ($this->gallery_m->delete_image($ids, $gallerytable, $gallerypath)) {
                $response = array('status'=> true, 'classe'=> 'success','message' => 'Exclusão efetuada com sucesso!');
            } else {
                $response = array('status'=> false, 'classe'=> 'error','message' => 'Ocorreu um erro durante a exclusão.');
            }
        } else {
            $response = array('status'=> false, 'classe'=> 'error','message' => 'Ocorreu um erro durante a exclusão.');
        }
        echo json_encode($response);
    }

    public function fallback($data)
    {
        $image = (isset($data['image']) && strlen($data['image']) > 0) ? $data['image']: null;
        if ($image) {
            delete_file(dirname(FCPATH) . DS . $this->input->post('gallerypath') . DS . $image);
        }
    }

    public function sort_images()
    {

        $this->gallery_m->sort($this->input->post('order'), $this->input->post('gallerytable'));

        $response = array(
            'status'=> true,
            'classe'=> 'warning',
            'message' => 'Ordem alterada com sucesso',
            'redirect' => false,
            'redirectModule' => null
        );

        echo json_encode($response);
    }

    private function _check_folder($folder)
    {
        // Cria o diretório (caso não exista) e já insere o .gitignore
        if (!file_exists($folder)) {
            @mkdir($folder, 0755);
            if(file_exists($folder)){
                $content = "*".PHP_EOL."!*/".PHP_EOL."!.gitignore";
                $fp = fopen($folder . "/.gitignore","wb");
                fwrite($fp,$content);
                fclose($fp);
            }
        }
    }
}