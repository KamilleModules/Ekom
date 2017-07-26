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
class GeneratedCoupon extends TableCrudObject
{

    public function __construct()
    {
        parent::__construct();
        $this->table = "ek_coupon";
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    protected function getCreateData(array $data)
    {
        $base = [
			'code' => '',
			'active' => 0,
			'mode' => '',
			'priority' => 0,
			'shop_id' => 0,
		];
        $ret = array_replace($base, array_intersect_key($data, $base));



        return $ret;
    }


}