<?php


namespace Module\Ekom\Helper;

class FormHelper
{

    public static function sanitizePrice($data)
    {
        return str_replace(',', '.', $data);
    }
}