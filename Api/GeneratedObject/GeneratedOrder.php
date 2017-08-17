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
class GeneratedOrder extends TableCrudObject
{

    public function __construct()
    {
        parent::__construct();
        $this->table = "ek_order";
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    protected function getCreateData(array $data)
    {
        $base = [
			'user_id' => 0,
			'reference' => '',
			'date' => '',
			'pay_identifier' => '',
			'tracking_number' => '',
			'user_info' => '',
			'shop_info' => '',
			'shipping_address' => '',
			'billing_address' => '',
			'order_details' => '',
		];
        $ret = array_replace($base, array_intersect_key($data, $base));



        return $ret;
    }


}