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
class GeneratedProductReference extends TableCrudObject
{

    public function __construct()
    {
        parent::__construct();
        $this->table = "ek_product_reference";
        $this->primaryKey = ['id'];
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    protected function getCreateData(array $data)
    {
        $base = [
			'id' => null,
			'product_id' => 0,
			'date_added' => '',
			'reference' => '',
			'price' => '',
			'quantity' => 0,
			'_product_details' => '',
			'extra1' => '',
			'extra2' => '',
			'is_orderable_when_out_of_stock' => 0,
			'out_of_stock_text' => '',
			'active' => 0,
			'is_available' => 0,
		];
        $ret = array_replace($base, array_intersect_key($data, $base));



        return $ret;
    }


}