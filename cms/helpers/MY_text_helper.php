<?php


function protect_download_ckeditor($text)
{
    $urlCkeditor = str_replace('cms/index.php', '', $_SERVER['SCRIPT_NAME']).'userfiles/ckeditor/';
    $size        = strlen($urlCkeditor);

    $dom = new DOMDocument;
    $dom->loadHTML($text);

    foreach ($dom->getElementsByTagName('a') as $node) {
        $url = $node->getAttribute('href');
        if (strpos($url, $urlCkeditor) !== FALSE) {
            $url = substr($url, $size);
            $node->setAttribute('href', download_site_url('ckeditor/' . $url));
        }
    }

    $new_html = $dom->saveHTML();
    $patterns = array("/^\<\!DOCTYPE.*?<html><body>/si", "!</body></html>$!si");
    $body_text = preg_replace($patterns, '', $new_html);
    return $body_text;
}

function character_limiter($str, $n = 500, $end_char = '&#8230;')
{
    if (strlen($str) < $n)
    {
        return $str;
    }

    $str = preg_replace("/\s+/", ' ', str_replace(array("\r\n", "\r", "\n"), ' ', $str));

    if (strlen($str) <= $n)
    {
        return $str;
    }

    $out = "";
    foreach (explode(' ', trim($str)) as $val)
    {
        $out .= $val.' ';

        if (strlen($out) >= $n)
        {
            $out = trim($out);
            return html_entity_decode((strlen($out) == strlen($str)) ? $out : $out.$end_char);
        }
    }
}

function word_limiter($str, $limit = 100, $end_char = '&#8230;')
{
    if (trim($str) == '')
    {
        return $str;
    }

    preg_match('/^\s*+(?:\S++\s*+){1,'.(int) $limit.'}/', $str, $matches);

    if (strlen($str) == strlen($matches[0]))
    {
        $end_char = '';
    }

    return html_entity_decode(rtrim($matches[0]).$end_char);
}