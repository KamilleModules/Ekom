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
    public static function getRelatedProductIds($cardId, $type = null)
    {
        if (null === $type) {
            $type = 'related-';
        }
        return A::cache()->get("Ekom.RelatedProductLayer.getRelatedProductIds.$type.$cardId", function () use ($cardId, $type) {
            return QuickPdo::fetchAll("
select h.product_id from ek_product_group_has_product h
inner join ek_product_group pg on pg.id=h.product_group_id
where pg.name=:name
", [
                'name' => ':' . $type . $cardId,
            ], \PDO::FETCH_COLUMN);
        });
    }

}