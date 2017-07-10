<?php


namespace Module\Ekom\ProductBox\AttributesModel\Generator;


use Module\Ekom\Utils\AttributeSelectorHelper;

class DefaultAttributesModelGenerator implements AttributesModelGeneratorInterface
{


    public function generate(array $productsInfo, $productId)
    {
        return AttributeSelectorHelper::adaptProductWithAttributesToAttributesModel($productsInfo, $productId);
    }

}