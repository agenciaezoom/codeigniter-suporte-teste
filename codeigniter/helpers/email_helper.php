<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter Email Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/helpers/email_helper.html
 */

// ------------------------------------------------------------------------

/**
 * Validate email address
 *
 * @access	public
 * @return	bool
 */
if ( ! function_exists('valid_email'))
{
	function valid_email($address)
	{
		return ( ! preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $address)) ? FALSE : TRUE;
	}
}

// ------------------------------------------------------------------------

/**
 * Send an email
 *
 * @access	public
 * @return	bool
 */
if ( ! function_exists('send_email'))
{
	function send_email($recipient, $subject = 'Test email', $message = 'Hello World')
	{
		return mail($recipient, $subject, $message);
	}
}

// ------------------------------------------------------------------------

/**
 * Send an HTML email, with layout standards
 *
 * @access	public
 * @return	bool
 */
if ( ! function_exists('enviar_email'))
{
	function enviar_email ($to, $subject, $body, $debug = false){

		$instanceName =& get_instance();
		$instanceName->load->helper('file');
		$instanceName->load->library('email');

		$config = $instanceName->config->item('config_email');
        $instanceName->email->initialize($config);
        $instanceName->email->set_newline("\r\n");
        $instanceName->email->clear(TRUE);

        $instanceName->email->from($config['smtp_user'], $config['smtp_username']);

        $instanceName->email->to($to);

        $instanceName->email->attach('application/modules/comum/assets/mail/images/logo.png', 'inline');
		$instanceName->email->attach('application/modules/comum/assets/mail/images/bg_header.png', 'inline');
		$instanceName->email->attach('application/modules/comum/assets/mail/images/spacer.gif', 'inline');
		$instanceName->email->attach('application/modules/comum/assets/mail/images/bg_bottom.png', 'inline');
		$instanceName->email->attach('application/modules/comum/assets/mail/images/divider_wide.png', 'inline');

        $message['base_url'] = site_url('application/modules/comum/assets/mail/');
		$message['data'] = date('d/m/Y - H:i:s');
        $message['title'] = $subject;

        if (is_array($body)){
	        foreach ($body as $key => $part) {
	        	$message['body'] .= "<strong>".$key.":</strong><br/>".addslashes(nl2br($part))."<br/><br/>";
	        }
        } else
        	$message['body'] = "<br/>".addslashes(nl2br($body))."<br/><br/>";

        $instanceName->email->subject($subject);
        $body_email = read_file('application/modules/comum/assets/mail/mail.html');
        $body_email = mail_replace($body_email, $message);

        $instanceName->email->message($body_email);

		if(!$instanceName->email->send()) {
        	if ($debug)
        		echo $instanceName->email->print_debugger();
        	return false;
        } else {
        	return true;
        }
	}
}




/* End of file email_helper.php */
/* Location: ./system/helpers/email_helper.php */