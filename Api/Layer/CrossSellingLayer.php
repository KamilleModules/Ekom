<?php


namespace Module\Ekom\Api\Layer;

use QuickPdo\QuickPdo;


/**
 * Cross selling can come from different sources.
 *
 * As for now:
 *
 * - related products (handled via ek_product_group :related-)
 * - accessory products (handled via ek_product_group :accessory-),
 *      not implemented yet since it might be included in the related products...
 *      but I just wanted to give the idea
 *
 *
 *
 *
 */
class CrossSellingLayer
{
    public static function getCrossSellProductIdsByProductId(int $productId)
    {
        /**
         * Assuming that all cross sells are implemented via the ek_product_group technique,
         * a hook should be done here to collect the identifiers (related, accessories, ....)
         * As for now, we only have the related identifier (and I'm the only dev),
         * so I will not use a hook yet.
         */
        $identifiers = [
            ':related-',
        ];


        $sIdentifiers = "'";
        $c = 0;
        foreach ($identifiers as $identifier) {
            if (0 !== $c) {
                $sIdentifiers .= "', '";
            }
            $sIdentifiers .= $identifier . $productId;
            $c++;
        }
        $sIdentifiers .= "'";



        $q = "
select 
h.product_id
from ek_product_group g 
inner join ek_product_group_has_product h on h.product_group_id=g.id 
where g.name in ($sIdentifiers) 
        
        ";


        $rows = QuickPdo::fetchAll($q, [], \PDO::FETCH_COLUMN);

        return $rows;

    }
}