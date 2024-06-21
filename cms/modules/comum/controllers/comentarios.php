<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Comentarios extends MY_Controller {

    public function __construct()
    {
        parent::__construct();

        $this->load->model('comentarios_m');

        $this->session->keep_flashdata();
    }

    public function index()
    {
        $this->template->set('title', T_('Esta página não existe ;('))
                       ->build('comum/error_404');
    }

    public function approve()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('id', 'ID', 'trim|required|integer');
        $this->form_validation->set_message('id', T_('ID do comentário inválido.'));

        if ($this->form_validation->run() === true){
            $this->comentarios_m->toggle_approve($this->input->post());
            $response = array('status'=> TRUE, 'classe'=> (($this->input->post('approve') == 'true') ? 'success' : 'warning'),'message' => (($this->input->post('approve') == 'true') ? T_('Comentário aprovado com sucesso!') : T_('Comentário desaprovado com sucesso!')));
        }else{
            $errors = array_values($this->form_validation->error_array());
            $response = array('status'=> FALSE, 'classe'=> 'error','message' => $errors[0]);
        }

        echo json_encode($response);
    }

    public function delete($id = FALSE)
    {
        if ($id) {
            $this->comentarios_m->delete($id);
            $response = array('status'=> TRUE, 'classe'=> 'success', 'message' => T_('Exclusão efetuada com sucesso!'));

        } else {
            $response = array('status'=> FALSE, 'classe'=> 'error','message' => T_('ID de exclusão inválido'));
        }

        $this->session->set_flashdata('message', $response);

        $this->load->library('user_agent');
        if ($this->agent->is_referral())
            redirect($this->agent->referrer());
        else
            redirect('comum/comentarios');
    }

    public function delete_multiple()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('id', 'ID', 'trim|required');
        $this->form_validation->set_message('id', T_('IDs de exclusão inválidos.'));

        if ($this->form_validation->run() === true){
            $this->comentarios_m->delete_multiple($this->input->post('id'));
            $response = array('status'=> TRUE, 'classe'=> 'success', 'message' => T_('Exclusão efetuada com sucesso!'));
        }else{
            $response = array('status'=> FALSE, 'classe'=> 'error','message' => T_('ID de exclusão inválido'));
        }
        $this->session->set_flashdata('message', $response);

        $this->load->library('user_agent');
        if ($this->agent->is_referral())
            redirect($this->agent->referrer());
        else
            redirect('comum/comentarios');
    }
}