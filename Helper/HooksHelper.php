<?php


namespace Module\Ekom\Helper;

use Kamille\Architecture\Registry\ApplicationRegistry;

class HooksHelper
{


    public static function Ekom_ProductBox_collectGeneralContext(array &$data){
        $data["shop_id"] = ApplicationRegistry::get("ekom.shop_id");
        $data["lang_id"] = ApplicationRegistry::get("ekom.lang_id");

        /**
         * @todo-ling: implement currency change in ekom !!
         */
        $data["currency_id"] = ApplicationRegistry::get("ekom.currency_id");
    }


}