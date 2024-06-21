<?php (defined('BASEPATH')) or exit('No direct script access allowed');

class MY_Session extends CI_Session
{
    /**
     * Keeps existing flashdata available to next request.
     *
     * @access    public
     * @param    string
     * @return void
     */
    public function keep_flashdata($key = null)
    {
        // 'old' flashdata gets removed.  Here we mark all
        // flashdata as 'new' to preserve it from _flashdata_sweep()
        // Note the function will NOT return FALSE if the $key
        // provided cannot be found, it will retain ALL flashdata

        if ($key === null) {
            foreach ($this->userdata as $k => $v) {
                $old_flashdata_key = $this->flashdata_key.':old:';
                if (strpos($k, $old_flashdata_key) !== false) {
                    $new_flashdata_key = $this->flashdata_key.':new:';
                    $new_flashdata_key = str_replace($old_flashdata_key, $new_flashdata_key, $k);
                    $this->set_userdata($new_flashdata_key, $v);
                }
            }

            return true;

        } elseif (is_array($key)) {
            foreach ($key as $k) {
                $this->keep_flashdata($k);
            }
        }

        $old_flashdata_key = $this->flashdata_key.':old:'.$key;
        $value = $this->userdata($old_flashdata_key);

        $new_flashdata_key = $this->flashdata_key.':new:'.$key;
        $this->set_userdata($new_flashdata_key, $value);
    }

    public function register_filter($module, $redirect)
    {
        $CI =& get_instance();
        // Filter
        if ($CI->input->post()) {
            $filter = array('module' => $module, 'search' => false, 'show' => 10);
            if ($this->flashdata('filter')) {
                $filter = array_merge($filter, $this->flashdata('filter'));
                $this->unset_userdata('flash:old:filter');
            }
            $post = $CI->input->post();
            if (isset($post['search']) && is_array($post['search'])){
                if (isset($post['search']['title'])) {
                    $post['search']['title'] = str_replace('\\','', $post['search']['title']);
                }
                foreach ($post['search'] as $key => $value) {
                    if (empty($value))
                        unset($post['search'][$key]);
                }
                if (empty($post['search']))
                    $post['search'] = FALSE;
            }
            $filter = array_merge($filter, $post);
            $this->set_flashdata('filter', $filter);
            redirect($redirect);
        } elseif ($CI->input->get('order')) {
            if ($CI->input->get('order') == 'false') {
                $this->set_flashdata('order_by', false);
            } else {
                $order_by = array('module' => $module, 'column' => $CI->input->get('order'));
                $order_by['order'] = 'desc';
                if ($this->flashdata('order_by')) {
                    $order = $this->flashdata('order_by');
                    if ($order_by['column'] == $order['column']) {
                        $order_by['order'] = $order['order'] == 'asc' ? 'desc' : 'asc';
                    }
                    $this->unset_userdata('flash:old:order_by');
                }
                $this->set_flashdata('order_by', $order_by);
            }
            redirect($redirect);
        }
    }

    public function keep_filter($modules)
    {
        $CI =& get_instance();
        $uri = $CI->uri->segment_array();
        if ($this->flashdata('filter')) {
            $filter = $this->flashdata('filter');
            $unset = true;
            foreach ($modules as $module) {
                if ($filter['module'] == $module /*and in_array($module, $uri)*/) {
                    $this->keep_flashdata('filter');
                    $unset = false;
                }
            }
            if ($unset) {
                $this->unset_userdata('flash:old:filter');
            }
        }
        if ($this->flashdata('order_by')) {
            $order_by = $this->flashdata('order_by');
            $unset2 = true;
            foreach ($modules as $module) {
                if ($order_by['module'] == $module /*and in_array($module, $uri)*/) {
                    $this->keep_flashdata('order_by');
                    $unset2 = false;
                }
            }
            if ($unset2) {
                $this->unset_userdata('flash:old:order_by');
            }
        }
    }

    public function sess_update()
    {
        if (!$this->CI->input->is_ajax_request()) {
            return parent::sess_update();
        }
    }
}
