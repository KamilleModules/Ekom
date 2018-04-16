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
class GeneratedDiscount extends TableCrudObject
{

    public function __construct()
    {
        parent::__construct();
        $this->table = "ek_discount";
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
			'type' => '',
			'value' => '',
			'apply_product_ids' => '',
			'apply_card_ids' => '',
			'apply_category_ids' => '',
			'priority' => 0,
			'active' => 0,
			'cond_date_start' => null,
			'cond_date_end' => null,
			'cond_user_group_id' => null,
			'cond_extra1' => null,
		];
        $ret = array_replace($base, array_intersect_key($data, $base));

        if (0 === (int)$ret["cond_user_group_id"]) {
            $ret["cond_user_group_id"] = null;
        }
        if ("" === $ret["cond_date_start"]) {
            $ret["cond_date_start"] = null;
        }
        if ("" === $ret["cond_date_end"]) {
            $ret["cond_date_end"] = null;
        }
        if ("" === $ret["cond_extra1"]) {
            $ret["cond_extra1"] = null;
        }


        return $ret;
    }


}