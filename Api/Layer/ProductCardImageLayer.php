<?php


namespace Module\Ekom\Api\Layer;


use Module\Ekom\Api\Object\ProductCardImage;
use QuickPdo\QuickPdo;

class ProductCardImageLayer
{

    public static function pushImageUrl($cardId, $url)
    {
        $o = new ProductCardImage();
        $o->push(["product_card_id" => $cardId], [
            'url' => $url,
            'product_card_id' => $cardId,
        ]);
    }


    /**
     * Return the product box images that should be displayed on the product box widget.
     */
    public static function getProductBoxImagesInfoByProductCardId(int $productCardId, int $productId = null)
    {

        $q = "
select
id,
legend,
is_default

from ek_product_card_image
where product_card_id=$productCardId        
";

        if (null !== $productId) {
            $q .= "
and (product_id is null or product_id=$productId or product_image_is_always_visible='1')
            
            ";
        } else {
            $q .= "and (product_is is null or product_image_is_always_visible='1')";
        }

        $q .= "
order by position asc        
        ";


        $rows = QuickPdo::fetchAll($q);
        foreach ($rows as $k => $row) {
            $uri = ImageLayer::getCardProductImageUriByImageId($row['id']);
            if (null !== $uri) {
                $rows[$k]['is_default'] = (bool)$row['is_default'];
                $rows[$k]['uri'] = ImageLayer::getCardProductImageUriByImageId($row['id']);
//            $rows[$k]['alt'] = $row['alt']; // alt should be given by the database, right?
            } else {
                unset($rows[$k]);
            }
        }

        return $rows;
    }
}