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
        $this->primaryKey = ['id'];
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    protected function getCreateData(array $data)
    {
        $base = [
			'id' => null,
			'label' => '',
			'code' => '',
			'description' => '',
			'highlight' => 0,
			'priority' => 0,
			'active' => 0,
			'cond_user_id' => null,
			'cond_date_start' => null,
			'cond_date_end' => null,
			'cond_minimum_amount' => null,
			'cond_country_id' => null,
			'cond_user_group_id' => null,
			'cond_cumulable_with_coupon_id' => null,
			'cond_rules' => null,
			'quantity' => null,
			'quantity_per_user' => null,
			'action_free_shipping' => 0,
			'action_type' => null,
			'action_value' => null,
		];
        $ret = array_replace($base, array_intersect_key($data, $base));

        if (0 === (int)$ret["cond_user_id"]) {
            $ret["cond_user_id"] = null;
        }
        if (0 === (int)$ret["quantity"]) {
            $ret["quantity"] = null;
        }
        if (0 === (int)$ret["quantity_per_user"]) {
            $ret["quantity_per_user"] = null;
        }
        if ("" === $ret["cond_date_start"]) {
            $ret["cond_date_start"] = null;
        }
        if ("" === $ret["cond_date_end"]) {
            $ret["cond_date_end"] = null;
        }
        if ("" === $ret["cond_minimum_amount"]) {
            $ret["cond_minimum_amount"] = null;
        }
        if ("" === $ret["cond_country_id"]) {
            $ret["cond_country_id"] = null;
        }
        if ("" === $ret["cond_user_group_id"]) {
            $ret["cond_user_group_id"] = null;
        }
        if ("" === $ret["cond_cumulable_with_coupon_id"]) {
            $ret["cond_cumulable_with_coupon_id"] = null;
        }
        if ("" === $ret["cond_rules"]) {
            $ret["cond_rules"] = null;
        }
        if ("" === $ret["action_type"]) {
            $ret["action_type"] = null;
        }
        if ("" === $ret["action_value"]) {
            $ret["action_value"] = null;
        }


        return $ret;
    }


}