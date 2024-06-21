<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Grupos extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('administracao_m');
        $this->load->model('grupos_m');
        $this->template
             ->add_metadata('keywords', 'codeigniter, nosparks')
             ->add_metadata('description', 'codeigniter without sparks');

        // Mantem sessão flashdata
        $this->session->keep_filter(array('administracao/grupos'));
    }

    public function index ($pg = 1){

        // Set Filtro
        $this->session->register_filter('administracao/grupos','administracao/grupos');
        $filter = ($this->session->flashdata('filter')) ? $this->session->flashdata('filter') : FALSE;
        $this->load->library('pagination');

        $search = $filter ? $filter['search'] : false;
        $total = $this->grupos_m->get_groups_total($search);
        $max = ($filter) ? (isset($filter['show']) ? $filter['show'] : 10) : 10;
        $start = ($pg - 1) * $max;

        $pagination = $this->pagination->init(array(
            'url' => site_url('administracao/grupos'),
            'total' => $total,
            'max' => $max,
            'segment' => 4 // Segment no qual o numero da pagina estará
        ));
        $this->template
             ->add_css('css/grupos')
             ->add_js('components/common/tables/datatables/assets/lib/js/jquery.dataTables.min.js', 'comum')
             ->add_js('components/modules/admin/modals/assets/js/bootbox.min.js', 'comum')
             ->add_js('components/common/tables/datatables/assets/custom/js/DT_bootstrap.js', 'comum')
             ->add_js('components/common/tables/datatables/assets/custom/js/datatables.init.js', 'comum')
             ->add_js('components/common/tables/datatables/assets/lib/extras/FixedHeader/FixedHeader.js', 'comum')
             ->add_js('components/common/tables/datatables/assets/lib/extras/ColReorder/media/js/ColReorder.min.js', 'comum')
             ->add_js('components/common/tables/responsive/assets/lib/js/footable.min.js', 'comum')
             ->add_js('components/common/tables/responsive/assets/custom/js/tables-responsive-footable.init.js', 'comum')
             ->add_js('components/common/tables/classic/assets/js/tables-classic.init.js', 'comum')
             ->add_js('components/common/forms/elements/bootstrap-select/assets/custom/js/bootstrap-select.init.js', 'comum')
             ->add_js('components/common/forms/elements/bootstrap-switch/assets/lib/js/bootstrap-switch.js', 'comum')
             ->add_js('components/common/forms/elements/bootstrap-switch/assets/custom/js/bootstrap-switch.init.js', 'comum')
             ->add_js('js/grupos')
             ->set('title', SITE_NAME.' - Grupos de Usuários')
             ->set('breadcrumb_route', array('administracao' => 'Administração', 'administracao/controle-de-usuarios' => 'Controle de Usuários', 'Grupos'))
             ->set('groups', $this->grupos_m->get_groups($search, $max, $start))
             ->set('paginacao', $pagination)
             ->set('search', $search)
             ->set('show', $max)
             ->build('grupos/grupos');
    }

    public function cadastrar (){
        $this->template
             ->add_css('css/grupos')
             ->add_js('components/common/forms/elements/bootstrap-switch/assets/lib/js/bootstrap-switch.js', 'comum')
             ->add_js('components/common/forms/elements/bootstrap-switch/assets/custom/js/bootstrap-switch.init.js', 'comum')
             ->add_js('components/common/forms/elements/fuelux-checkbox/fuelux-checkbox.js', 'comum')
             ->add_js('components/common/forms/validator/assets/lib/jquery-validation/dist/jquery.validate.min.js', 'comum')
             ->add_js('components/modules/admin/widgets/widget-collapsible/assets/widget-collapsible.init.js', 'comum')
             ->add_js('js/grupos')
             ->set('title', SITE_NAME.' - Cadastrar Grupos de Usuários')
             ->set('breadcrumb_route', array('administracao' => 'Administração', 'administracao/controle-de-usuarios' => 'Controle de Usuários',  'administracao/grupos' => 'Grupos', 'Cadastrar'))
             ->set('modules', $this->administracao_m->get_modules_type())
             ->build('grupos/cadastrar');
    }

    public function editar ($id){

        $id || show_404();

        $group = $this->grupos_m->get_group($id);
        $group->flags = json_decode($group->flags, true);
        $group->permissions = json_decode($group->permissions, true);

        $this->template
             ->add_css('css/grupos')
             ->add_js('components/common/forms/elements/bootstrap-switch/assets/lib/js/bootstrap-switch.js', 'comum')
             ->add_js('components/common/forms/elements/bootstrap-switch/assets/custom/js/bootstrap-switch.init.js', 'comum')
             ->add_js('components/common/forms/elements/fuelux-checkbox/fuelux-checkbox.js', 'comum')
             ->add_js('components/common/forms/validator/assets/lib/jquery-validation/dist/jquery.validate.min.js', 'comum')
             ->add_js('components/modules/admin/widgets/widget-collapsible/assets/widget-collapsible.init.js', 'comum')
             ->add_js('js/grupos')
             ->set('title', SITE_NAME.' - Editar Grupos de Usuários')
             ->set('breadcrumb_route', array('administracao' => 'Administração', 'administracao/controle-de-usuarios' => 'Controle de Usuários',  'administracao/grupos' => 'Grupos', 'Editar'))
             ->set('id', $id)
             ->set('group', $group)
             ->set('existing_permissions', $group->permissions)
             ->set('modules', $this->administracao_m->get_modules_type())
             ->build('grupos/editar');
    }

    public function add (){

        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', 'Nome', 'trim|required');

        if ($this->form_validation->run() === true){

            $this->grupos_m->insert_group($this->input->post());
            $response = array('status'=> TRUE, 'classe'=> 'success','message' => T_('Registro inserido com sucesso!'), 'redirect' => TRUE, 'redirectModule' => 'administracao/grupos');

        } else {
            $errors = array_values($this->form_validation->error_array());
            $response = array('status'=> FALSE, 'classe'=> 'error','message' => $errors[0], 'redirect' => FALSE);
        }

        echo json_encode($response);
    }

    public function edit ($id){

        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', 'Nome', 'trim|required');
        $this->form_validation->set_rules('id', 'ID', 'trim|required|integer');
        $this->form_validation->set_message('id', 'ID de edição inválido.');

        if ($this->form_validation->run() === true and $id){

            $this->grupos_m->update_group($this->input->post());
            $response = array('status'=> TRUE, 'classe'=> 'success','message' => T_('Registro editado com sucesso!'), 'redirect' => TRUE, 'redirectModule' => 'administracao/grupos');

        } else {

            $errors = array_values($this->form_validation->error_array());
            $response = array('status'=> FALSE, 'classe'=> 'error','message' => $errors[0], 'redirect' => FALSE);

        }

        echo json_encode($response);
    }

    public function delete ($id){

        if ($this->input->post('delete') == 'true' && $id){

            $this->grupos_m->delete_group($id);
            $response = array('status'=> TRUE, 'classe'=> 'success', 'message' => T_('Exclusão efetuada com sucesso!'));

        } else {

            $response = array('status'=> FALSE, 'classe'=> 'error','message' => 'Não foi possível realizar a exclusão.');

        }

        echo json_encode($response);
    }

    public function delete_multiple (){

        $this->load->library('form_validation');
        $this->form_validation->set_rules('id', 'ID', 'trim|required');
        $this->form_validation->set_message('id', 'IDs de exclusão inválidos.');

        if ($this->form_validation->run() === true){

            $this->grupos_m->delete_group_multiple($this->input->post('id'));
            $response = array(
                'status'=> TRUE,
                'classe'=> 'success',
                'message' => 'Registros excluidos com sucesso!',
                'id' => $this->input->post('id')
            );

        } else {
            $response = array(
                'status'=> FALSE,
                'classe'=> 'error',
                'message' => 'Ocorreu um erro durante a exclusão multipla.'
            );
        }

        echo json_encode($response);

    }

    public function active (){

        $this->load->library('form_validation');
        $this->form_validation->set_rules('id', 'ID', 'trim|required|integer');
        $this->form_validation->set_rules('actived', T_('Status'), 'trim|required');
        $this->form_validation->set_message('id', T_('ID de ativação inválido.'));

        if ($this->form_validation->run() === true){
            $this->grupos_m->toggle_status_group($this->input->post());
            $response = array('status'=> TRUE, 'classe'=> (($this->input->post('actived') == 'true') ? 'success' : 'warning'),'message' => (($this->input->post('actived') == 'true') ? 'Registro ativado com sucesso!' : 'Registro desativado com sucesso!'));
        }else{
            $errors = array_values($this->form_validation->error_array());
            $response = array('status'=> FALSE, 'classe'=> 'error','message' => $errors[0]);
        }

        echo json_encode($response);
    }

}

/* End of file controle-de-usuarios.php */
/* Location: ./application/controllers/controle-de-usuarios.php */
