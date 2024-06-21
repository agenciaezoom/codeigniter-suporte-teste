<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_User_agent extends CI_User_agent{

    protected function _compile_data()
    {
        $this->_set_platform();

        foreach (array('_set_mobile', '_set_robot', '_set_browser') as $function)
        {
            if ($this->$function() === TRUE)
            {
                if ($function != '_set_mobile'){
                    break;
                }
            }
        }
    }

}


/* End of file User_agent.php */
/* Location: ./system/libraries/User_agent.php */