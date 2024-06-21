<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class MY_Session extends CI_Session {
    /**
     * Keeps existing flashdata available to next request.
     *
     * @access    public
     * @param    string
     * @return    void
     */
    function keep_flashdata($key=NULL)
    {
        // 'old' flashdata gets removed.  Here we mark all
        // flashdata as 'new' to preserve it from _flashdata_sweep()
        // Note the function will NOT return FALSE if the $key
        // provided cannot be found, it will retain ALL flashdata

        if($key === NULL){
            foreach($this->userdata as $k => $v){
                $old_flashdata_key = $this->flashdata_key.':old:';
                if(strpos($k, $old_flashdata_key) !== false){
                    $new_flashdata_key = $this->flashdata_key.':new:';
                    $new_flashdata_key = str_replace($old_flashdata_key, $new_flashdata_key, $k);
                    $this->set_userdata($new_flashdata_key, $v);
                }
            }
            return true;

        } elseif(is_array($key)){
            foreach($key as $k){
                $this->keep_flashdata($k);
            }
        }

        $old_flashdata_key = $this->flashdata_key.':old:'.$key;
        $value = $this->userdata($old_flashdata_key);

        $new_flashdata_key = $this->flashdata_key.':new:'.$key;
        $this->set_userdata($new_flashdata_key, $value);
    }
}