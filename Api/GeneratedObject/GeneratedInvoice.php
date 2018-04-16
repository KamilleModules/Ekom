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
class GeneratedInvoice extends TableCrudObject
{

    public function __construct()
    {
        parent::__construct();
        $this->table = "ek_invoice";
        $this->primaryKey = ['id'];
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    protected function getCreateData(array $data)
    {
        $base = [
			'id' => null,
			'user_id' => null,
			'order_id' => 0,
			'seller_id' => null,
			'label' => '',
			'invoice_number' => '',
			'invoice_number_alt' => null,
			'invoice_date' => '',
			'payment_method' => '',
			'shop_host' => '',
			'track_identifier' => '',
			'amount' => '',
			'seller' => '',
			'user_info' => '',
			'seller_address' => '',
			'shipping_address' => '',
			'billing_address' => '',
			'invoice_details' => '',
		];
        $ret = array_replace($base, array_intersect_key($data, $base));

        if (0 === (int)$ret["user_id"]) {
            $ret["user_id"] = null;
        }
        if (0 === (int)$ret["seller_id"]) {
            $ret["seller_id"] = null;
        }
        if ("" === $ret["invoice_number_alt"]) {
            $ret["invoice_number_alt"] = null;
        }


        return $ret;
    }


}