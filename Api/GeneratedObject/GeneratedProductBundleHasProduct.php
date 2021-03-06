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
class GeneratedProductBundleHasProduct extends TableCrudObject
{

    public function __construct()
    {
        parent::__construct();
        $this->table = "ek_product_bundle_has_product";
        $this->primaryKey = ['product_bundle_id', 'product_id'];
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    protected function getCreateData(array $data)
    {
        $base = [
			'product_bundle_id' => 0,
			'product_id' => 0,
			'quantity' => 0,
		];
        $ret = array_replace($base, array_intersect_key($data, $base));



        return $ret;
    }


}