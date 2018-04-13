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
class GeneratedProductHasProductAttribute extends TableCrudObject
{

    public function __construct()
    {
        parent::__construct();
        $this->table = "ek_product_has_product_attribute";
        $this->primaryKey = ['product_id', 'product_attribute_id'];
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    protected function getCreateData(array $data)
    {
        $base = [
			'product_id' => 0,
			'product_attribute_id' => 0,
			'product_attribute_value_id' => 0,
		];
        $ret = array_replace($base, array_intersect_key($data, $base));



        return $ret;
    }


}