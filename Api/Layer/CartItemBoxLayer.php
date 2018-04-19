<?php


namespace Module\Ekom\Api\Layer;

use Core\Services\A;
use Core\Services\Hooks;
use Module\Ekom\Api\Util\ProductQueryBuilderUtil;
use QuickPdo\QuickPdo;


/**
 * 2018-04-19
 *
 * This object represents a cart item.
 * A cart item is like the miniBox model,
 * @see EkomModels::miniBoxModel()
 *
 * but adds a few more properties, like the tax details, the discount details,
 * the attributes selected by the user, the product details selected by the user.
 *
 *
 *
 *
 *
 *
 */
class CartItemBoxLayer
{


    public static function getBox(string $productId, array $selectedProductDetails=[])
    {


            // note: product details might be able to change the core of the base query?
            $sqlQuery = ProductQueryBuilderUtil::getBaseQuery();
            $sqlQuery->addWhere("and p.id=$productId");

            $row = QuickPdo::fetch((string)$sqlQuery, $sqlQuery->getMarkers());
            self::sugarify($row);
            return $row;
    }





    public static function sugarify(array &$row)
    {
        MiniProductBoxLayer::sugarify($row);

        // todo: selected attributes
        // todo: selected product details
        // todo: tax details
        // todo: discount item

        $row['has_tax'] = ('1.00' === $row['tax_ratio']);
        $row['is_novelty'] = (false !== strpos($row['codes'], 'n'));
        $row['product_uri'] = A::link("Ekom_productCardRef", [
            "slug" => $row['product_card_slug'],
            "ref" => $row['reference'],
        ]);
        $row['image'] = ImageLayer::getCardProductImageUriByImageId($row['image_id']);
        $row['image_title'] = (!empty($row['image_legend']) ? $row["image_legend"] : $row['label']);
        $row['image_alt'] = $row["label"];
        $row['has_discount'] = (null !== $row['discount_type']);
    }


}