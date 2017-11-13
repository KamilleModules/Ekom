<?php


namespace Module\Ekom\Helper;

use Kamille\Architecture\Registry\ApplicationRegistry;
use Module\Ekom\Utils\Checkout\CurrentCheckoutData;

class HooksHelper
{

    public static function Ekom_CheckoutPageUtil_onStepCompleted($stepName, array $data)
    {
        throw new \Exception("todo, inject shipping into CurrentCheckoutData");
        switch ($stepName) {
            case "shipping":
                break;
            case "payment":
                break;
            default:
                break;
        }
    }

    public static function Ekom_ProductBox_collectGeneralContext(array &$data)
    {
        $data["shop_id"] = ApplicationRegistry::get("ekom.shop_id");
        $data["lang_id"] = ApplicationRegistry::get("ekom.lang_id");

        /**
         * @todo-ling: implement currency change in ekom !!
         */
        $data["date"] = date('Y-m-d');
        $data["currency_id"] = ApplicationRegistry::get("ekom.currency_id");
    }


}