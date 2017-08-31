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
class GeneratedShopHasPaymentMethod extends TableCrudObject
{

    public function __construct()
    {
        parent::__construct();
        $this->table = "ek_shop_has_payment_method";
        $this->primaryKey = ['shop_id', 'payment_method_id'];
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    protected function getCreateData(array $data)
    {
        $base = [
			'shop_id' => 0,
			'payment_method_id' => 0,
			'order' => 0,
			'configuration' => '',
		];
        $ret = array_replace($base, array_intersect_key($data, $base));



        return $ret;
    }


}