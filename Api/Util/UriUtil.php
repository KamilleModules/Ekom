<?php


namespace Module\Ekom\Api\Util;



use Module\Ekom\Utils\E;

class UriUtil
{


    public static function getProductBoxBaseAjaxUri($productId){
        return E::link("Ekom_ajaxApi") . "?action=getProductInfo&id=" . $productId;
    }
}