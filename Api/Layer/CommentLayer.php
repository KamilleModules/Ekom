<?php


namespace Module\Ekom\Api\Layer;


use Core\Services\A;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;

class CommentLayer
{


    public function getRatingInfo($cardId)
    {

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
product_id in ($sIds)
and active=1
            
            
            ");

            $info['average'] = round($info['average'], 2);

        }
        return $info;
    }


    public function getProductCommentInfo()
    {


        return A::cache()->get("Ekom.CommentLayer.getProductCommentInfo", function ()  {
            return QuickPdo::fetchAll("
select 

product_id,
count(*) as nbComments,
sum(rating) as sum,
avg(rating) as average

from ek_product_comment

group by product_id
        ");
        }, [
            'ek_product_comment',
        ]);
    }
}
