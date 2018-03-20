<?php


namespace Module\Ekom\Models\Product\Comment;


use Kamille\Architecture\Registry\ApplicationRegistry;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Models\Iterator\IteratorTrait;
use QuickPdo\QuickPdo;

class CommentListModel implements \Iterator
{

    private $myArray;
    use IteratorTrait;


    public static function createByProductId($productId, $shopId = null)
    {
        $productId = (int)$productId;
        if (null === $shopId) {

            $shopId = (int)ApplicationRegistry::get("ekom.shop_id");
        }
        $rows = QuickPdo::fetchAll("select id from ek_product_comment where 
    product_id=$productId and shop_id=$shopId");


        $o = new static();

        foreach ($rows as $row) {
            $o->myArray[] = CommentModel::createByRow($row);
        }
        return $o;
    }

}