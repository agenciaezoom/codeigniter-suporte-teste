<?php (defined('BASEPATH')) or exit('No direct script access allowed');

/**
 * Controller
 *
 * @package ezoom
 * @subpackage Rotas
 * @category Controller
 * @author Diogo Taparello
 * @copyright 2016 Ezoom
 */
class routes extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index($pg = 1)
    {
        parent::index($pg);
    }

    public function cadastrar($id = false)
    {
        if($this->auth->data('admin') && $id){
            parent::editar($id);
            $this->template->set('copy', true);
        }
        $this->formulario();
    }

    public function editar($id)
    {
        parent::editar($id);
        $this->formulario($id);
    }

    protected function formulario($id = false)
    {
        parent::formulario($id);
    }

    public function dump()
    {
        $id = $this->input->post('id');
        if ($id){
            $item = $this->model->get( array( 'id' => $id ) );

            $params = array();
            $vars = get_object_vars($item);
            foreach ($vars as $key => $value) {
                if (!is_array($value) && $key != 'languages' && $key != 'id'){
                    $params['`'.$key.'`'] = is_null($value) ? 'NULL' : "'".$value."'";
                }
            }
            $queries = 'INSERT INTO `ez_route` ('.implode(', ',array_keys($params)).') VALUES <br>('.implode(', ', $params).'); <br><br>';
            $queries .= 'SET @last_id = LAST_INSERT_ID(); <br><br>';

            if (!empty($item->languages)){
                $keys = array();
                $vars = get_object_vars(current($item->languages));
                foreach ($vars as $key => $value) {
                    $keys[] = '`'.$key.'`';
                }
                $queries .= 'INSERT INTO `ez_route_description` ('.implode(', ',$keys).') VALUES <br>';
                $inserts = array();
                foreach ($item->languages as $key => $value) {
                    $values = array();
                    $vars = get_object_vars($value);
                    foreach ($vars as $key => $value) {
                        if ($key != 'id'){
                            $values[] = is_null($value) ? 'NULL' : ($key=='id_route' ? '@last_id' : "'".$value."'");
                        }
                    }
                    $inserts[] = '('.implode(', ', $values).')';
                }
                $queries .= implode(',<br>',$inserts).';';
            }
            $this->load->view('modal', array(
                'title' => $item->label,
                'queries' => $queries
            ));
        }else{
            if($this->db->dbdriver == 'mysqli') {
                $backup = $this->db->backup(array(
                    'tables'    => array('ez_route', 'ez_route_description'),
                    'format'    => 'txt',
                    'ignore'    => array(),
                    'add_drop'  => TRUE,
                    'add_insert'=> TRUE,
                    'newline'   => "\n"
                ));
            } else {
                $this->load->dbutil();
                $backup = $this->dbutil->backup(array(
                    'tables'    => array('ez_route', 'ez_route_description'),
                    'format'    => 'txt'
                ));
            }
            header("Content-Type: text/plain");
            print_r($backup);
        }
    }
}
