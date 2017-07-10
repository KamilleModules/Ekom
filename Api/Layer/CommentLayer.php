<?php


namespace Module\Ekom\Api\Layer;


use Kamille\Architecture\Registry\ApplicationRegistry;
use Module\Ekom\Api\EkomApi;
use QuickPdo\QuickPdo;

class CommentLayer
{


    public function getRatingInfo($cardId, $shopId = null)
    {
        EkomApi::inst()->initWebContext();
        $shopId = (null === $shopId) ? ApplicationRegistry::get("ekom.shop_id") : (int)$shopId;

        $cardId = (int)$cardId;
        $ids = QuickPdo::fetchAll("select id from ek_product where product_card_id=$cardId", [], \PDO::FETCH_COLUMN);
        $info = [];
        if ($ids) {

            $sIds = implode(", ", $ids);
            $info = QuickPdo::fetch("
select 
count(*) as count,
avg(rating) as average
from ek_product_comment 
where 
shop_id=$shopId            
and product_id in ($sIds)
and active=1
            
            
            ");

            $info['average'] = round($info['average'], 2);

        }
        return $info;

    }
}
