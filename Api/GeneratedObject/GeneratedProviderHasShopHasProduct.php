<?php


namespace Module\Ekom\Api\GeneratedObject;


use XiaoApi\Object\TableCrudObject;

/**
 * This file was generated by the DbObjectGenerator.
 * You should not edit it manually, otherwise you
 * might loose your edits on the next update.
 *
 * You are supposed to extend this object.
 */
class GeneratedProviderHasShopHasProduct extends TableCrudObject
{

    public function __construct()
    {
        parent::__construct();
        $this->table = "ek_provider_has_shop_has_product";
        $this->primaryKey = ['provider_id', 'shop_has_product_shop_id', 'shop_has_product_product_id'];
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    protected function getCreateData(array $data)
    {
        $base = [
			'provider_id' => 0,
			'shop_has_product_shop_id' => 0,
			'shop_has_product_product_id' => 0,
		];
        $ret = array_replace($base, array_intersect_key($data, $base));



        return $ret;
    }


}