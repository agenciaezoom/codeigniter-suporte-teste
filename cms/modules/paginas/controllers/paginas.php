<?php (defined('BASEPATH')) or exit('No direct script access allowed');

class Paginas extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index($pg = 1)
    {
        $areas = $this->paginas_m->get_areas();
        $subareas = $this->paginas_m->get_areas(TRUE);

        $this->template
             ->set('subareas', $subareas)
             ->set('areas', $areas);

        parent::index($pg);
    }

    public function cadastrar()
    {
        $this->fileupload();

        $this->template
             ->add_css('css/gallery', 'gallery')
             ->add_js('js/gallery', 'gallery');

        $this->formulario();
    }

    public function editar($id)
    {

        $permission = $this->model->getPermission($id);

        $this->template
             ->set('permission', $permission);

        parent::editar($id);
        $this->formulario($id);
    }

    protected function formulario($id = false)
    {
        $this->ckeditor();
        $this->fileupload();
        $this->template
             ->add_css('css/gallery', 'gallery')
             ->add_js('js/gallery', 'gallery');
        parent::formulario($id);
    }

    public function permissoes($id)
    {
        $id || show_404();

        $item = $this->model->get(array('id' => $id));

        $item || show_404();

        $permission = $this->model->getPermission($id);

        $this->template
             ->set('title', SITE_NAME.' - Permissões')
             ->set('breadcrumb_route', array('paginas' => 'Páginas', 'Permissões'))
             ->set('id', $id)
             ->set('item', $item)
             ->set('permission', $permission)
             ->build($this->view);
    }

    public function savePermission($id)
    {
        if ($id) {
            if ($this->model->updatePermissions($id, $this->input->post())) {
                $response = array('status'=> true, 'classe'=> 'success','message' => 'Registro editado com sucesso!', 'redirect' => true, 'redirectModule' => 'paginas');
            } else {
                $this->fallback($this->input->post());
                $response = array('status'=> false, 'classe'=> 'error', 'message' => 'Ocorreu um erro inesperado na edição', 'redirect' => false);
            }
        } else {
            $this->fallback($this->input->post());
            $errors = array_values($this->form_validation->error_array());
            $response = array('status'=> false, 'classe'=> 'error','message' => $errors[0], 'redirect' => false);
        }

        echo json_encode($response);
    }
}
