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
class GeneratedPurchaseProductStat extends TableCrudObject
{

    public function __construct()
    {
        parent::__construct();
        $this->table = "ek_purchase_product_stat";
        $this->primaryKey = ['id'];
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    protected function getCreateData(array $data)
    {
        $base = [
			'purchase_date' => '',
			'shop_id' => 0,
			'user_id' => 0,
			'currency_id' => 0,
			'product_id' => 0,
			'product_label' => '',
			'quantity' => 0,
			'price' => '',
			'total' => '',
			'attribute_selection' => '',
			'product_details_selection' => '',
		];
        $ret = array_replace($base, array_intersect_key($data, $base));



        return $ret;
    }


}