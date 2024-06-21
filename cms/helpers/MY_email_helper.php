<?php (defined('BASEPATH')) or exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package     CodeIgniter
 * @author      ExpressionEngine Dev Team
 * @copyright   Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license     http://codeigniter.com/user_guide/license.html
 * @link        http://codeigniter.com
 * @since       Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter Email Helpers
 *
 * @package     CodeIgniter
 * @subpackage  Email
 * @category    Helpers
 */

// ------------------------------------------------------------------------

/**
 * Altera as variaveis do template do email para o valor informado
 *
 * @access  public
 * @return  bool
 */
if (! function_exists('mail_replace')) {
    function mail_replace($body_email, $array)
    {
        foreach ($array as $key => $value) {
            $body_email = str_replace('{'.$key.'}', $value, $body_email);
        }

        return $body_email;
    }
}

/**
 * Caso não exista a função mb_convert, retorna a string sem alteração
 */
if(!function_exists('mb_convert_encoding')) {
    function mb_convert_encoding($str, $to_encoding, $from_encoding) {
        return $str;
    }
}

if (! function_exists('enviar_email')) {
    /**
     * Função que envia email com template em comum/assets/mail/
     * @param  array        $emails  array('to' => array('mail1', 'mail2'), 'cc' => array('mail1', 'mail2'), 'bcc' => array('...''), 'replyTo' = 'string')
     * @param  string       $subject descrição do título do email
     * @param  array|string $body    conteúdo do email
     * @param  boolean      $debug   exibir erros
     * @return boolean
     */
    function enviar_email($emails = array(), $subject = null, $body = array(), $debug = false)
    {
        //Verifica se pelo menos existe email para enviar
        if (is_array($emails) && !isset($emails['to'])) {
            if ($debug) {
                echo 'Não existe $emails["to"]';
            }

            return false;
        }

        try {
            $instanceName =& get_instance();
            $config = $instanceName->config->item('config_email');

            if(isset($config['mail_api_provider'])){
                switch ($config['mail_api_provider']) {
                    case 'sendgrid':
                        return send_by_sendgrid($emails, $subject, $body, $debug, $instanceName);
                        break;
                    case 'mandrill':
                        if(isset($config['mandrill_api']) && $config['mandrill_api']){
                            return send_by_api($emails, $subject, $body, $debug, $instanceName);
                        }
                        break;
                    default:
                        return send_by_smtp($emails, $subject, $body, $debug, $instanceName);
                        break;
                }
            }else{
                return send_by_smtp($emails, $subject, $body, $debug, $instanceName);
            }

        } catch (Exception $e) {
            if ($debug) {
                echo $e->getMessage();
            }

            return false;
        }
    }
}

if (! function_exists('send_by_api')) {
    /**
     * Função que envia email com template em comum/assets/mail/
     * @param  array        $emails  array('to' => array('mail1', 'mail2'), 'cc' => array('mail1', 'mail2'), 'bcc' => array('...''), 'replyTo' = 'string')
     * @param  string       $subject descrição do título do email
     * @param  array|string $body    conteúdo do email
     * @param  boolean      $debug   exibir erros
     * @return boolean
     */
    function send_by_api($emails = array(), $subject = null, $body = array(), $debug = false, $instanceName = false)
    {
        $config = $instanceName->config->item('config_email');

        require_once APPPATH.'libraries/Mandrill.php'; //Not required with Composer
        $mandrill = new Mandrill($config['smtp_pass']);

        $message = array(
            'subject' => $subject,
            'from_email' => $config['mandrill_from'],
            'from_name' => $config['smtp_username'],
            'to' => is_array($emails['to']) ? $emails['to'] : array($emails['to']),
            'headers' => array('Reply-To' => isset($emails['replyTo']) ? $emails['replyTo'] : $config['mandrill_from']),
            'important' => false,
            'track_opens' => null,
            'track_clicks' => null,
            'auto_text' => null,
            'auto_html' => null,
            'inline_css' => null,
            'url_strip_qs' => null,
            'preserve_recipients' => null,
            'view_content_link' => null,
            'tracking_domain' => null,
            'signing_domain' => null,
            'return_path_domain' => null,
            'merge' => true,
            'merge_language' => 'mailchimp',
            'metadata' => array('website' => site_url()),
        );

        if(is_array($emails['to'])){
            foreach ($emails['to'] as $key => $email) {
                $to[] = array(
                    'email' => $email,
                    'name' => $email,
                    'type' => 'to'
                );
            }
        }else{
            $to[] = array(
                'email' => $emails['to'],
                'name' => $emails['to'],
                'type' => 'to'
            );
        }

        if(isset($emails['cc'])){
            if(is_array($emails['cc'])){
                foreach ($emails['cc'] as $key => $email) {
                    $to[] = array(
                        'email' => $email,
                        'name' => $email,
                        'type' => 'cc'
                    );
                }
            }else{
                $to[] = array(
                    'email' => $emails['cc'],
                    'name' => $emails['cc'],
                    'type' => 'cc'
                );
            }
        }

        $message['to'] = $to;

        if(isset($emails['bcc'])){
            $message['bcc_address'] = $emails['bcc'];
        }

        $images = array();
        $attachments = array();

        //Logo
        if (file_exists(APPPATH . 'userfiles/logo.png')) {
            $images[] = array(
                'type' => 'image/png',
                'name' => 'logo.png',
                'content' => base64_encode(file_get_contents(APPPATH . 'userfiles/logo.png'))
            );
        }else{
            $images[] = array(
                'type' => 'image/png',
                'name' => 'logo.png',
                'content' => base64_encode(file_get_contents(APPPATH . 'modules/comum/assets/mail/images/logo.png'))
            );
        }

        if (is_array($body)) {
            if (isset($body['archive'])){
                if (is_array($body['archive'])) {
                    foreach ($body['archive'] as $filename => $disposition) {
                        $filename = is_string($filename) ? $filename : $disposition;
                        $disposition = in_array($disposition, array('inline', 'attachment')) ? $disposition : null;
                        if (file_exists(FCPATH . $filename)) {
                            if($disposition == 'inline'){
                                $images[] = array(
                                    'type' => 'image/png',
                                    'name' => $filename,
                                    'content' => base64_encode(file_get_contents(FCPATH . $filename))
                                );
                            }

                            if($disposition == 'attachment'){
                                $attachments[] = array(
                                    'type' => 'text/plain',
                                    'name' => $filename,
                                    'content' => base64_encode(file_get_contents(FCPATH . $filename))
                                );
                            }
                        }
                    }

                } else {
                    if (file_exists(FCPATH . $body['archive'])) {
                        $attachments[] = array(
                            'type' => 'text/plain',
                            'name' => $body['archive'],
                            'content' => base64_encode(file_get_contents(FCPATH . $body['archive']))
                        );
                    }
                }

                unset($body['archive']);
            }

            $message['images'] = $images;
            $message['attachments'] = $attachments;

            $message['html'] = '<br/>';
            foreach ($body as $key => $part) {
                $message['html'] .= "<strong>".$key.":</strong><br/>".addslashes(nl2br($part))."<br/><br/>";
            }
        } else {
            $message['html'] = "<br/>".$body."<br/><br/>";
        }

        $template['base_url'] = site_url(APPPATH . 'modules/comum/assets/mail/');
        $template['link_logo'] = site_url();
        $template['alt_logo'] = 'logo.png';
        $template['data'] = date('d/m/Y - H:i:s');
        $template['title'] = $subject;
        $template['body'] = $message['html'];
        $template['summary'] = null;
        $template['address'] = null;

        $body_email = read_file(APPPATH . 'modules/comum/assets/mail/mail.html');
        $body_email = mail_replace($body_email, $template);

        $message['html'] = $body_email;

        $async = false;
        if(!$mandrill->messages->send($message, $async)){
            return false;
        }else{
            return true;
        }

    }
}

if (! function_exists('send_by_smtp')) {
    /**
     * Função que envia email com template em comum/assets/mail/
     * @param  array        $emails  array('to' => array('mail1', 'mail2'), 'cc' => array('mail1', 'mail2'), 'bcc' => array('...''), 'replyTo' = 'string')
     * @param  string       $subject descrição do título do email
     * @param  array|string $body    conteúdo do email
     * @param  boolean      $debug   exibir erros
     * @return boolean
     */
    function send_by_smtp($emails = array(), $subject = null, $body = array(), $debug = false, $instanceName = false)
    {
        $config = $instanceName->config->item('config_email');

        $instanceName->load->helper('file');
        $instanceName->load->library('email');

        if (count($config)==0) {
            throw new Exception("Você deve definir as configurações de e-mail.");
        }

        $instanceName->email->initialize($config);
        $instanceName->email->set_newline("\r\n");
        $instanceName->email->set_crlf("\r\n");
        $instanceName->email->clear(true);

        if (!isset($config['smtp_username'])) {
            $config['smtp_username'] = $config['smtp_user'];
        }

        if (!isset($config['smtp_email'])) {
            $config['smtp_email'] = $config['smtp_user'];
        }

        $instanceName->email->from($config['smtp_user'], $config['smtp_username']);
        $instanceName->email->to($emails['to']);

        if (isset($emails['cc'])) {
            $instanceName->email->cc($emails['cc']);
        }

        if (isset($emails['bcc'])) {
            $instanceName->email->bcc($emails['bcc']);
        }

        if (isset($emails['replyTo'])) {
            $instanceName->email->reply_to($emails['replyTo']);
        }

        if (file_exists(APPPATH . 'userfiles/logo.png')) {
            $instanceName->email->attach(APPPATH . 'userfiles/logo.png', 'inline');
        } else {
            $instanceName->email->attach(APPPATH . 'modules/comum/assets/mail/images/logo.png', 'inline');
        }

        $message['base_url'] = site_url(APPPATH . 'modules/comum/assets/mail/');
        $message['link_logo'] = site_url();
        $message['alt_logo'] = 'logo.png';
        $message['data'] = date('d/m/Y - H:i:s');
        $message['title'] = $subject;
        $message['body'] = null;
        $message['summary'] = null;
        $message['address'] = null;

        if (is_array($body)) {
            if (isset($body['archive'])){
                if (is_array($body['archive'])) {
                    foreach ($body['archive'] as $filename => $disposition) {
                        $filename = is_string($filename) ? $filename : $disposition;
                        $disposition = in_array($disposition, array('inline', 'attachment')) ? $disposition : null;
                        if (file_exists(FCPATH . $filename)) {
                            $instanceName->email->attach(FCPATH . $filename, $disposition ? $disposition : 'attachment');
                        }
                    }
                } else {
                    if (file_exists(FCPATH . $body['archive'])) {
                        $instanceName->email->attach(FCPATH . $body['archive']);
                    }
                }
                unset($body['archive']);
            }

            foreach ($body as $key => $part) {
                $message['body'] .= "<strong>".$key.":</strong><br/>".addslashes(nl2br($part))."<br/><br/>";
            }
        } else {
            $message['body'] = "<br/>".$body."<br/><br/>";
        }

        $message['preheader'] = strip_tags(html_entity_decode($message['body']));
        $instanceName->email->set_alt_message($message['preheader']);

        $instanceName->email->subject($subject);
        $body_email = read_file(APPPATH . 'modules/comum/assets/mail/mail.html');
        $body_email = mail_replace($body_email, $message);

        $instanceName->email->message($body_email);

        if (!$instanceName->email->send()) {
            if ($debug) {
                echo $instanceName->email->print_debugger();
            }

            return false;
        } else {
            return true;
        }
    }
}

if (! function_exists('send_by_sendgrid')) {
    /**
     * Função que envia email com template em comum/assets/mail/
     * @param  array        $emails  array('to' => array('mail1', 'mail2'), 'cc' => array('mail1', 'mail2'), 'bcc' => array('...''), 'replyTo' = 'string')
     * @param  string       $subject descrição do título do email
     * @param  array|string $body    conteúdo do email
     * @param  boolean      $debug   exibir erros
     * @return boolean
     */
    function send_by_sendgrid($emails = array(), $subject = null, $body = array(), $debug = false, $instanceName = false)
    {
        $config = $instanceName->config->item('config_email');

        require_once APPPATH.'libraries/SendGrid.php';
        $sendgrid = new \SendGrid($config['smtp_pass']);

        $email = new \SendGrid\Mail\Mail();
        $email->setSubject($subject);
        $email->setFrom($config['mail_from'], $config['smtp_username']);

        $replyTo = isset($emails['replyTo']) ? $emails['replyTo'] : $config['mail_from'];
        $email->setReplyTo($replyTo, $replyTo);

        if(is_array($emails['to'])){
            foreach ($emails['to'] as $key => $to) {
                $email->addTo($to, $to);
            }
            // $email->addTos($tos);
        }else{
            $email->addTo($emails['to'], $emails['to']);
        }

        if(isset($emails['cc'])){
            if(is_array($emails['cc'])){
                foreach ($emails['cc'] as $key => $cc) {
                    $email->addCc($cc, $cc);
                }
                // $email->addCcs($ccs);
            }else{
                $email->addCc($emails['cc'], $emails['cc']);
            }
        }

        if(isset($emails['bcc'])){
            if(is_array($emails['bcc'])){
                foreach ($emails['bcc'] as $key => $bcc) {
                    $email->addBcc($bbc, $bbc);
                }
                // $email->addBccs($bccs);
            }else{
                $email->addBcc($emails['bcc'], $emails['bcc']);
            }
        }

        $attachments = array();

        //Logo
        $attachments[] = array(
            base64_encode(file_get_contents(APPPATH . 'modules/comum/assets/mail/images/logo.png')),
            "image/png",
            "logo.png",
            "inline",
            "logo.png"
        );

        if (is_array($body)) {
            if (isset($body['archive'])){
                if (is_array($body['archive'])) {
                    foreach ($body['archive'] as $filename => $disposition) {
                        $filename = is_string($filename) ? $filename : $disposition;
                        $disposition = in_array($disposition, array('inline', 'attachment')) ? $disposition : null;
                        if (file_exists(FCPATH . $filename)) {
                            $attachments[] = array(
                                base64_encode(file_get_contents(FCPATH . $filename)),
                                "image/png",
                                $filename,
                                $disposition
                            );
                        }
                    }

                } else {
                    if (file_exists(FCPATH . $body['archive'])) {
                        $attachments[] = array(
                            base64_encode(file_get_contents(FCPATH . $body['archive'])),
                            "text/plain",
                            $body['archive'],
                            "attachment"
                        );
                    }
                }

                unset($body['archive']);
            }

            $content = '<br/>';
            foreach ($body as $key => $part) {
                $content .= "<strong>".$key.":</strong><br/>".addslashes(nl2br($part))."<br/><br/>";
            }
        } else {
            $content = "<br/>".$body."<br/><br/>";
        }

        $email->addAttachments($attachments);

        $template['base_url'] = site_url(APPPATH . 'modules/comum/assets/mail/');
        $template['link_logo'] = site_url();
        $template['alt_logo'] = 'logo.png';
        $template['data'] = date('d/m/Y - H:i:s');
        $template['title'] = $subject;
        $template['body'] = $content;
        $template['summary'] = null;
        $template['address'] = null;

        $body_email = read_file(APPPATH . 'modules/comum/assets/mail/mail.html');
        $body_email = mail_replace($body_email, $template);

        $content = $body_email;

        $email->addContent(
            "text/html",
            $content
        );

        try {
            $response = $sendgrid->send($email);
            return $response->statusCode() == 202;
        } catch (Exception $e) {
            return false;
        }
    }
}

