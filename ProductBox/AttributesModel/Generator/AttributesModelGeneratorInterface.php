<?php


namespace Module\Ekom\ProductBox\AttributesModel\Generator;


interface AttributesModelGeneratorInterface
{


    /**
     * @param array $productsInfo , see ProductLayer.getProductCardProductsWithAttributes for more info,
     *          of ProductLayer.getProductBoxModelByCardId method.
     *
     *
     *
     * @param $productId , the product id chosen to represent the card.
     *
     * @return array attributesModel, see productBox model documentation for more information
     */
    public function generate(array $productsInfo, $productId);

}