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
class GeneratedProductCombinationHasProduct extends TableCrudObject
{

    public function __construct()
    {
        parent::__construct();
        $this->table = "ek_product_combination_has_product";
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    protected function getCreateData(array $data)
    {
        $ret = array_replace([
			'product_combination_id' => 0,
			'product_id' => 0,
			'quantity' => 0,
			'active' => 0,
		], $data);



        return $ret;
    }


}