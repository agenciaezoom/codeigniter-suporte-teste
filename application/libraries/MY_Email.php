<?php  (defined('BASEPATH')) or exit('No direct script access allowed');

/**
 * Corrigido envios de imagens inline
 * Agora o script adiciona Contend-ID: <image.ext>
 */
class MY_Email extends CI_Email
{
    /**
     * Build Final Body and attachments
     *
     * @access  protected
     * @return void
     */
    protected function _build_message()
    {
        if ($this->wordwrap === true  and  $this->mailtype != 'html') {
            $this->_body = $this->word_wrap($this->_body);
        }

        $this->_set_boundaries();
        $this->_write_headers();

        $hdr = ($this->_get_protocol() == 'mail') ? $this->newline : '';
        $body = '';

        switch ($this->_get_content_type()) {
            case 'plain':

                $hdr .= "Content-Type: text/plain; charset=" . $this->charset . $this->newline;
                $hdr .= "Content-Transfer-Encoding: " . $this->_get_encoding();

                if ($this->_get_protocol() == 'mail') {
                    $this->_header_str .= $hdr;
                    $this->_finalbody = $this->_body;
                } else {
                    $this->_finalbody = $hdr . $this->newline . $this->newline . $this->_body;
                }

                return;

            break;
            case 'html':

                if ($this->send_multipart === false) {
                    $hdr .= "Content-Type: text/html; charset=" . $this->charset . $this->newline;
                    $hdr .= "Content-Transfer-Encoding: quoted-printable";
                } else {
                    $hdr .= "Content-Type: multipart/alternative; boundary=\"" . $this->_alt_boundary . "\"" . $this->newline . $this->newline;

                    $body .= $this->_get_mime_message() . $this->newline . $this->newline;
                    $body .= "--" . $this->_alt_boundary . $this->newline;

                    $body .= "Content-Type: text/plain; charset=" . $this->charset . $this->newline;
                    $body .= "Content-Transfer-Encoding: " . $this->_get_encoding() . $this->newline . $this->newline;
                    $body .= $this->_get_alt_message() . $this->newline . $this->newline . "--" . $this->_alt_boundary . $this->newline;

                    $body .= "Content-Type: text/html; charset=" . $this->charset . $this->newline;
                    $body .= "Content-Transfer-Encoding: quoted-printable" . $this->newline . $this->newline;
                }

                $this->_finalbody = $body . $this->_prep_quoted_printable($this->_body) . $this->newline . $this->newline;

                if ($this->_get_protocol() == 'mail') {
                    $this->_header_str .= $hdr;
                } else {
                    $this->_finalbody = $hdr . $this->_finalbody;
                }

                if ($this->send_multipart !== false) {
                    $this->_finalbody .= "--" . $this->_alt_boundary . "--";
                }

                return;

            break;
            case 'plain-attach':

                $hdr .= "Content-Type: multipart/".$this->multipart."; boundary=\"" . $this->_atc_boundary."\"" . $this->newline . $this->newline;

                if ($this->_get_protocol() == 'mail') {
                    $this->_header_str .= $hdr;
                }

                $body .= $this->_get_mime_message() . $this->newline . $this->newline;
                $body .= "--" . $this->_atc_boundary . $this->newline;

                $body .= "Content-Type: text/plain; charset=" . $this->charset . $this->newline;
                $body .= "Content-Transfer-Encoding: " . $this->_get_encoding() . $this->newline . $this->newline;

                $body .= $this->_body . $this->newline . $this->newline;

                break;
            case 'html-attach':

                $hdr .= "Content-Type: multipart/".$this->multipart."; boundary=\"" . $this->_atc_boundary."\"" . $this->newline . $this->newline;

                if ($this->_get_protocol() == 'mail') {
                    $this->_header_str .= $hdr;
                }

                $body .= $this->_get_mime_message() . $this->newline . $this->newline;
                $body .= "--" . $this->_atc_boundary . $this->newline;

                $body .= "Content-Type: multipart/alternative; boundary=\"" . $this->_alt_boundary . "\"" . $this->newline .$this->newline;
                $body .= "--" . $this->_alt_boundary . $this->newline;

                $body .= "Content-Type: text/plain; charset=" . $this->charset . $this->newline;
                $body .= "Content-Transfer-Encoding: " . $this->_get_encoding() . $this->newline . $this->newline;
                $body .= $this->_get_alt_message() . $this->newline . $this->newline . "--" . $this->_alt_boundary . $this->newline;

                $body .= "Content-Type: text/html; charset=" . $this->charset . $this->newline;
                $body .= "Content-Transfer-Encoding: quoted-printable" . $this->newline . $this->newline;

                $body .= $this->_prep_quoted_printable($this->_body) . $this->newline . $this->newline;
                $body .= "--" . $this->_alt_boundary . "--" . $this->newline . $this->newline;

                break;
        }

        $attachment = array();

        $z = 0;

        for ($i=0; $i < count($this->_attach_name); $i++) {
            $filename = $this->_attach_name[$i];
            $basename = basename($filename);
            $ctype = $this->_attach_type[$i];

            if (! file_exists($filename)) {
                $this->_set_error_message('lang:email_attachment_missing', $filename);

                return false;
            }

            $h  = "--".$this->_atc_boundary.$this->newline;

            if ($this->_attach_disp[$i] == 'inline') {
                $h .= "Content-ID: <".$basename.">".$this->newline;
            }

            $h .= "Content-type: ".$ctype."; ";
            $h .= "name=\"".$basename."\"".$this->newline;
            $h .= "Content-Disposition: ".$this->_attach_disp[$i].";".$this->newline;
            $h .= "Content-Transfer-Encoding: base64".$this->newline;

            $attachment[$z++] = $h;
            $file = filesize($filename) +1;

            if (! $fp = fopen($filename, FOPEN_READ)) {
                $this->_set_error_message('lang:email_attachment_unreadable', $filename);

                return false;
            }

            $attachment[$z++] = chunk_split(base64_encode(fread($fp, $file)));
            fclose($fp);
        }

        $body .= implode($this->newline, $attachment).$this->newline."--".$this->_atc_boundary."--";

        if ($this->_get_protocol() == 'mail') {
            $this->_finalbody = $body;
        } else {
            $this->_finalbody = $hdr . $body;
        }

        return;
    }
}
