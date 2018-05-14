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
class GeneratedOrderPurchaseStatSeller extends TableCrudObject
{

    public function __construct()
    {
        parent::__construct();
        $this->table = "ek_order_purchase_stat_seller";
        $this->primaryKey = ['id'];
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    protected function getCreateData(array $data)
    {
        $base = [
			'id' => null,
			'order_id' => 0,
			'seller_label' => '',
			'amount' => '',
			'quantity' => 0,
			'date_purchase' => '',
		];
        $ret = array_replace($base, array_intersect_key($data, $base));



        return $ret;
    }


}