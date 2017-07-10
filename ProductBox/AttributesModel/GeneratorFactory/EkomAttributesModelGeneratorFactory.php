<?php


namespace Module\Ekom\ProductBox\AttributesModel\GeneratorFactory;


use Module\Ekom\Exception\EkomException;
use Module\Ekom\ProductBox\AttributesModel\Generator\AttributesModelGeneratorInterface;
use Module\Ekom\ProductBox\AttributesModel\Generator\DefaultAttributesModelGenerator;

class EkomAttributesModelGeneratorFactory extends AttributesModelGeneratorFactory
{
    public function __construct()
    {
        parent::__construct();
        $this->setDefaultGenerator(new DefaultAttributesModelGenerator());
    }


}