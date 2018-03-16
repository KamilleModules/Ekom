<?php


namespace Module\Ekom\Api\Object;


use Module\Ekom\Api\GeneratedObject\GeneratedProductCardImage;


class ProductCardImage extends GeneratedProductCardImage
{
    public function __construct()
    {
        parent::__construct();

        $this->addListener([
            'createAfter',
            'updateAfter',

        ], function ($eventName, $table, $data, $thing) {
            a(func_get_args());
            a("doo");
        });
    }


}