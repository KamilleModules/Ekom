<?php


namespace Module\Ekom\Api\Layer;


use Module\Ekom\Api\Object\ProductVariation;

class ProductVariationLayer
{


    /**
     * Update or insert a record for the given productReferenceId and conditionIdentifier,
     * and set the price of this record to the given price.
     *
     *
     * @param int $productReferenceId
     * @param string $conditionIdentifier
     * @param $newPrice
     */
    public static function defineProductVariationPriceByProductReferenceIdConditionIdentifier(int $productReferenceId, string $conditionIdentifier, $newPrice)
    {
        ProductVariation::getInst()->push([
            "product_reference_id" => $productReferenceId,
            "cond_identifier" => $conditionIdentifier,
        ], [
            'price' => $newPrice,
        ]);
    }
}



