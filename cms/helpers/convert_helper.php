<?php (defined('BASEPATH')) or exit('No direct script access allowed');

if (!function_exists('floatToUS')) {
    function floatToUS($value)
    {
        $value = str_replace('R$', '', $value);
        $value = str_replace('%', '', $value);
        $value = str_replace('cm', '', $value);
        $value = str_replace(' ', '', $value);
        $value = str_replace('.', '', $value);
        $value = str_replace(',', '.', $value);

        return $value;
    }
}

if (!function_exists('floatToBR')) {
    function floatToBR($value)
    {
        $value = number_format($value, 2, ',', '.');
        return $value;
    }
}

if (!function_exists('dateToUS')) {
    function dateToUS($date)
    {
        $date = DateTime::createFromFormat('d/m/Y', $date);
        return $date->format('Y-m-d');
    }
}

if (!function_exists('dateToBR')) {
    function dateToBR($date)
    {
        $date = DateTime::createFromFormat('Y-m-d', $date);
        return $date->format('d/m/Y');
    }
}