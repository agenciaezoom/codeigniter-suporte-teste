<?php (defined('BASEPATH'))  or exit('No direct script access allowed');

/**
 * Download -> Downloads
 *
 */
class Download extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('download');

        // Mantém all the flashdata
        $this->session->keep_flashdata();
    }

    public function index($path = array())
    {
        $file = array_pop($path);
        $folder = implode('/', $path);
        $folder = str_replace('-', '_', $folder);
        $file = str_replace('_', '-', $file);

        //Para evitar problemas de _ e - enviei da seguinte forma: download_url('pasta/aqruivo.pdf');
        if(empty($folder)){
            $path = base64url_decode($file);
            $path = explode('/', $path);
            $file = array_pop($path);
            $folder = implode('/', $path);
        }

        ($folder && $file) or show_404();

        if (strpos($file, '../') !== false or strpos($folder, '../') !== false) {
            show_404();
        }

        $dir = dirname(FCPATH) . '/userfiles/'.$folder.'/';

        if (file_exists($dir.$file)) {
            $this->do_download($dir, $file);
        } else {
            show_404();
        }
    }

    public function folder_as_zip($path = array())
    {
        $allowed = array('documentos');
        $check  = current($path);
        $name   = end($path);
        $folder = implode('/', $path);
        $folder = str_replace('_', '-', $folder);

        ($folder && $name) or show_404();

        if (!$folder or strpos($folder, '../') !== false or !in_array($check, $allowed)) {
            show_404();
        }

        $dir = dirname(FCPATH) . '/userfiles/'.$folder.'/';

        if (is_dir($dir)) {
            // Confere se a pasta é a última e não contem nenhuma subpasta
            $results = scandir($dir);
            foreach ($results as $result) {
                if ($result === '.' or $result === '..') {
                    continue;
                }
                if (is_dir($dir . '/' . $result)) {
                    show_404();
                }
            }

            $this->load->library('zip');
            $this->zip->read_dir($dir, false);
            $this->zip->download($name.'.zip');
        } else {
            show_404();
        }
    }

    /**
     * Faz o download propriamente dito
     * @param  [string] $dir
     * @param  [string] $file
     * @return [download]
     */
    private function do_download($dir, $file) {
        if(strstr($file, '..') || strstr($file, 'php'))
            exit('Há');

        // Generate the server headers
        if (strpos($_SERVER['HTTP_USER_AGENT'], "MSIE") !== FALSE) {
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.$file.'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header("Content-Transfer-Encoding: binary");
            header('Pragma: public');
            header("Content-Length: ".filesize($dir.$file));
        } else {
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.$file.'"');
            header("Content-Transfer-Encoding: binary");
            header('Expires: 0');
            header('Pragma: no-cache');
            header("Content-Length: ".filesize($dir.$file));
        }

        $handle = fopen($dir.$file, 'rb');
        $buffer = '';
        while (!feof($handle)) {
            $buffer = fread($handle, 4096);
            echo $buffer;
            ob_flush();
            flush();
        }
        fclose($handle);
        //readfile($dir.$file);
        exit;
    }

    public function get_current_module()
    {
        return true;
    }

    public function _remap($method, $params = array())
    {
        if (method_exists($this, $method)) {
            return call_user_func(array($this, $method), $params);
        } else {
            array_unshift($params, $method);
            $this->index($params);
        }
    }
}

/* End of file Download.php*/
/* Location: ./modules/Download/controllers/Download.php */
