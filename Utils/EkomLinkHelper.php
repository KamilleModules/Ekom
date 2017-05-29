<?php


namespace Module\Ekom\Utils;


/**
 * This is a private helper, used by me and other developers who want to use it.
 * The idea is to centralize the creation of uri which form I'm not sure yet.
 */
class EkomLinkHelper
{


    public static function getUri($type, $param1 = null)
    {
        switch ($type) {
            case 'removeProductFromCart':
                // param1=product_id
                return "/service/Ekom/gscp/api?action=removeProductFromCart&product_id=" . $param1;
                break;
            case 'updateCartProduct':
                // param1=product_id
                return "/service/Ekom/gscp/api?action=updateCartProduct&product_id=" . $param1 . '&qty'; // need to append "=$qty" to it
                break;
            default:
                throw new \Exception("Unknown type: $type");
                break;
        }
    }
}