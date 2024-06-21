<?php if ( ! defined('BASEPATH')){exit('No direct script access allowed'); }

/**
 * Model
 *
 * @package ezoom
 * @subpackage administracao
 * @category Model
 * @author Fábio Bachi
 * @copyright 2014 Ezoom
 */
class administracao_m extends MY_Model
{

    /**
     * Metodo construtor
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function get_modules_type ()
    {
        $data = $this->get_modules();

        $return = array();
        foreach ($data as $key => $module){
            $return[$module->system][$module->id] = $module;
        }
        return $return;
    }

    private function get_modules ($idModule = null)
    {
        $this->db->select('ez_module.*, ez_module_description.name, ez_module_description.slug')
                 ->from('ez_module')
                 ->join('ez_module_description', 'ez_module_description.id_module = ez_module.id and ez_module_description.id_language = (SELECT id_language FROM ez_company_description WHERE ez_company_description.id_company='.$this->auth->data('company').' AND id_language IN (1,'.$this->current_lang.') ORDER BY id_language DESC limit 1)', 'left')
                 ->where('ez_module.status', '1')
                 ->order_by('ez_module.order_by', 'ASC');

        if ($idModule)
            $this->db->where('id_parent', $idModule);
        else
            $this->db->where('id_parent', null);

        $query = $this->db->get();
        $data = $query->result();

        foreach ($data as $key => $menu) {
            $menu->children = $this->get_modules($menu->id);
        }

        return $data;

    }

}
?>