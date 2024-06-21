<?php (defined('BASEPATH')) or exit('No direct script access allowed');

/**
 * Model
 *
 * @package ezoom
 * @subpackage comum
 * @category Model
 * @author Fábio Bachi
 * @copyright 2016 Ezoom
 */
class comum_m extends MY_Model
{
    public function get_languages($company = null)
    {
        if( isset($company) ){
            //PRINCIPAL
            $this->db->select('*')
                     ->from('ez_language')
                     ->where('status', 1)
                     ->where('ez_language.id', $company->language_main);

            $query = $this->db->get();
            $result = $query->result();

            //RELACIONADAS
            $this->db->select('*')
                     ->from('ez_language')
                     ->where('status', 1)
                     ->order_by('id')
                     ->where_in('ez_language.id', explode(',', $company->languages_site) )
                     ->where('ez_language.id != ', $company->language_main);

            $query = $this->db->get();
            $result = array_merge($result, $query->result() );

            //RESTANTE
            $this->db->select('*')
                     ->from('ez_language')
                     ->where('status', 1)
                     ->order_by('id')
                     ->where_not_in('ez_language.id', explode(',', $company->languages_site) )
                     ->where('ez_language.id != ', $company->language_main);

            $query = $this->db->get();
            $result = array_merge($result, $query->result() );

        } else {
            $this->db->select('*')
                     ->from('ez_language')
                     ->where('status', 1)
                     ->order_by('id');

            $query = $this->db->get();
            $result = $query->result();
        }

        return $result;
    }

    /**
     * Retorna os estados
     * @author Diogo Taparello [diogo@ezoom.com.br]
     * @date   2016-07-26
     */
    public function get_state( $params = array() )
    {
        $options = array(
            'id' => FALSE, //consulta pelo id do estado
            'uf' => FALSE, //consulta pela uf do estado
            'name' => FALSE, //consulta pelo nome do estado
        );
        $params = array_merge($options, $params);

        $this->db->select('*')
                 ->from('ez_state')
                 ->order_by('id');

        if( $params['id'] )
            $this->db->where('id', $params['id']);

        if( $params['uf'] )
            $this->db->like('LCASE(uf)', strtolower($params['uf']) );

        if( $params['name'] ) {
            $this->db->where('LCASE(name)', strtolower($params['name']));
            $this->db->or_like('LCASE(name)', strtolower($params['name']));
        }

        $query = $this->db->get();

        if($params['uf'] || $params['id'] || $params['name'])
            $return = $query->row();
        else
            $return = $query->result();

        return $return;
    }

    /**
     * Retorna as cidades
     * @author Diogo Taparello [diogo@ezoom.com.br]
     * @date   2016-07-26
     */
    public function get_city( $params = array() )
    {
        $options = array(
            'id' => FALSE, //consulta pelo id da cidade
            'name' => FALSE, //consulta pela nome da cidade
            'id_state' => FALSE, //consulta as cidades do estado
        );
        $params = array_merge($options, $params);

        $this->db->select('*')
                 ->from('ez_city');

        if( $params['id'] )
            $this->db->where('id', $params['id']);

        if( $params['id_state'] )
            $this->db->where('id_state', $params['id_state']);

        if( $params['name'] )
            $this->db->like('LCASE(name)', strtolower($params['name']) );

        $query = $this->db->get();

        if($params['id'] || $params['name'])
            $return = $query->row();
        else
            $return = $query->result();

        return $return;
    }

    public function get_countries()
    {
        $this->db->select('*')
                 ->from('ez_country')
                 ->join('ez_country_description', 'ez_country.id = ez_country_description.id_country', 'INNER')
                 ->where('ez_country_description.id_language', 1)
                 ->order_by('name');

        $query = $this->db->get();

        return $query->result();
    }

    public function get_user_permissions()
    {
        $this->db->select('permissions')
                 ->from('ez_user')
                 ->where('id', $this->auth->data('id'));

        $query = $this->db->get();
        $user = $query->row();

        return json_decode($user->permissions);

    }

    /**
     * Retorna o modulo pesquisado pela slug recebida.
     * @author Fabio Bachi [fabio.bachi@ezoom.com.br]
     * @date   2014-08-22
     * @param  string   $slug Slug para pesquisa do modulo.
     * @return stdClass Objeto contendo o modulo.
     */
    public function get_current_module($slug, $alternative = null)
    {
        $system = pathinfo(FCPATH, PATHINFO_BASENAME);
        if($system != 'cms' && strstr($system, 'cms'))
            $system = 'cms';

        $this->db->select('*')
                 ->from('ez_module')
                 ->join('ez_module_description', 'ez_module.id = ez_module_description.id_module', 'left')
                 ->where_in('ez_module_description.id_language', array($this->current_lang, '1') )
                 ->where('system',  $system)
                 ->where_in( 'slug', array( str_replace('_', '-', $slug), str_replace('_', '-', $alternative) ) )
                 ->order_by('ez_module_description.id_language', 'DESC');

        $query = $this->db->get();
        return $query->row();

    }
}
