<?php


namespace Module\Ekom\Api\Layer;


use Core\Services\A;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;

class RelatedProductLayer
{


    /**
     * We use the :related- prefix to implement the related products.
     * See notes in database.
     */
    public function getRelatedProductIds($cardId, $shopId = null)
    {

        if (null === $shopId) {
            $shopId = E::getShopId();
        }
        $shopId = (int)$shopId;
        $productId = (int)$cardId;

        return A::cache()->get("Ekom.RelatedProductLayer.getRelatedProductIds.$shopId.$cardId", function () use ($cardId, $shopId) {
            return QuickPdo::fetchAll("
select h.product_id from ek_product_group_has_product h
inner join ek_product_group pg on pg.id=h.product_group_id
where pg.name=:name
and pg.shop_id=$shopId
", [
                'name' => ':related-' . $cardId,
            ], \PDO::FETCH_COLUMN);
        }, [
            'ek_product_group_has_product',
            'ek_product_group',
        ]);
    }

}