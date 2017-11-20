<?php


namespace Module\Ekom\Helper;

use Kamille\Architecture\Registry\ApplicationRegistry;
use Module\Ekom\Utils\Checkout\CurrentCheckoutData;
use Module\Ekom\Utils\E;

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
        $data["shop_id"] = E::getShopId();
        $data["lang_id"] = E::getLangId();

        /**
         * @todo-ling: implement currency change in ekom !!
         */
        $data["date"] = date('Y-m-d');
        $data["currency_id"] = ApplicationRegistry::get("ekom.currency_id");
    }


}