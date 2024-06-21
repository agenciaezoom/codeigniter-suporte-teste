<?php (defined('BASEPATH')) or exit('No direct script access allowed');

class Contato extends MY_Controller
{
    /**
     * Lista de campos com alias utilizados para reenvio do e-mail.
     *
     * @var array
     */
    protected $fields = array(
        'name'    => 'Nome',
        'email'   => 'E-mail',
        'phone'   => 'Telefone',
        'mobile'  => 'Celular',
        'subject' => 'Assunto',
        'state'   => 'Estado',
        'city'    => 'Cidade',
        'message' => 'Mensagem',
    );

/*
    protected $params = array(
        'where' => array(
            'area' => 'Trabalhe conosco',
        ),
    );

    public function __construct()
    {
        parent::__construct();
        $this->class = 'contato';
        $this->load_module();
    }
*/
    /**
     * Seta o contato como visualizado e retorna.
     *
     * @author Detley Oliveira [detley@ezoom.com.br]
     * @date   2016-08-12
     * @return view
     */
    public function visualizar()
    {
        $id = $this->input->post('id');
        $item = $this->contato_m->get( array( 'id' => $id ) );

        if ($item->status != 'Respondida') {
            $this->contato_m->visualizar($id);
            $item->status = 'Respondida';
        }

        $this->load->view(
            'contato/modal',
            array(
                'item' => $item
            )
        );
    }

    /**
     * Seta o contato como respondido.
     *
     * @author Detley Oliveira [detley@ezoom.com.br]
     * @date   2016-08-12
     * @param  int     $id
     * @return bool
     */
    public function responder($id)
    {
        return $this->contato_m->responder($id);
    }
    /**
     * Seta o contato como visualizado e reenvia o e-mail conforme campo receivers.
     *
     * @author Detley Oliveira [detley@ezoom.com.br]
     * @date   2016-08-12
     * @param  int     $id
     * @return bool
     */
    public function reenviar($id)
    {
        $contact = $this->contato_m->get( array( 'id' => $id ) );

        $send = $this->sendEmail($contact);

        if ($contact->status != 'Respondida' && $send)
            $this->contato_m->visualizar($id);

        return $this->toJson(array('status' => $send));
    }

    /**
     * Reenvia o e-mail de contato conforme o campo "receivers".
     * Setar o campo receivers no front ao tentar enviar e-mail.
     * Decalarar no $fields os campos que podem ser enviados caso preenchido.
     *
     * @author Detley Oliveira [detley@ezoom.com.br]
     * @date   2016-08-12
     * @param  DataObject     $contact
     * @return bool
     */
    protected function sendEmail($contact)
    {
        if(! $contact->receivers)
            return false;

        $this->load->helper('email_helper');

        $subject = $contact->area;
        $emails['to'] = explode(',', $contact->receivers);

        foreach($this->fields as $field => $alias)
            if ($contact->{$field})
                $body[$alias] = mb_convert_encoding($contact->{$field}, "utf-8", "auto");

        return enviar_email($emails, $subject, $body);
    }

    /**
     * Exportar tabela para csv
     * @author Diogo Taparello [diogo@ezoom.com.br]
     * @date   2016-07-28
     * @return [type]     [description]
     */
    public function exportar()
    {
        $results = $this->contato_m->export(@$this->params);

        $fields_cnt = $results['fields_cnt'];
        $result = $results['result'];

        $schema_insert = '';
        foreach ($fields_cnt as $key => $value) {
            $list = '"' . str_replace(
                '"',
                "\\" . '"',
                stripslashes($value)
            ) . '"';
            $schema_insert .= $list;
            $schema_insert .= ";";
        }
        $out = trim(substr($schema_insert, 0, -1));
        $out .= "\n";

        $totalFields = count($fields_cnt);

        foreach ($result as $key => $row) {
            $schema_insert = '';
            foreach ($fields_cnt as $k => $value) {
                if ($row->$value == '0' || $row->$value != '') {
                    if ('"' == '') {
                        $schema_insert .= mb_convert_encoding($row->$value,'utf-16','utf-8');
                    } else {
                        $schema_insert .= '"' .
                        str_replace('"', "\\" . '"', mb_convert_encoding($row->$value,'utf-16','utf-8')) . '"';
                    }
                } else {
                    $schema_insert .= '';
                }
                if ($k < $totalFields - 1) {
                    $schema_insert .= ";";
                }
            }
            $out .= $schema_insert;
            $out .= "\n";
        }

        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Length: " . strlen($out));
        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=".$this->current_module->slug."_".date('d-m-Y').".csv");
        echo $out;
        exit;
    }
}