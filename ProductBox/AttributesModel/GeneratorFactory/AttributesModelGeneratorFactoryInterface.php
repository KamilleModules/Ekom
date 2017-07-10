<?php


namespace Module\Ekom\ProductBox\AttributesModel\GeneratorFactory;


use Module\Ekom\ProductBox\AttributesModel\Generator\AttributesModelGeneratorInterface;

interface AttributesModelGeneratorFactoryInterface
{


    /**
     * @param $generatorInfo , array containing the following structure:
     *      - product_type: the product type
     *      - extra: an arbitrary string created by modules for their own convenience
     *
     *
     * @return AttributesModelGeneratorInterface
     */
    public function get(array $generatorInfo);

}