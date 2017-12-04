<?php


namespace Module\Ekom\Utils\InvoiceNumberProvider;


class InvoiceNumberProvider implements InvoiceNumberProviderInterface
{

    public static function create()
    {
        return new static();
    }

    public function getNumber($type = null)
    {
        list($usec, $sec) = explode(" ", microtime());
        $decimalLength = 3;
        $ret = date("Ymd_His_") . substr(substr($usec, 2), 0, $decimalLength);
        return "$type-" . $ret;
    }


}