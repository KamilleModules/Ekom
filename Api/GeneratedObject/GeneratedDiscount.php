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
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    protected function getCreateData(array $data)
    {
        $base = [
			'user_group_id' => null,
			'currency_id' => null,
			'date_start' => null,
			'date_end' => null,
			'procedure_type' => '',
			'procedure_operand' => '',
			'target' => '',
			'shop_id' => 0,
		];
        $ret = array_replace($base, array_intersect_key($data, $base));

        if (0 === (int)$ret["user_group_id"]) {
            $ret["user_group_id"] = null;
        }
        if (0 === (int)$ret["currency_id"]) {
            $ret["currency_id"] = null;
        }
        if (0 === (int)$ret["date_start"]) {
            $ret["date_start"] = null;
        }
        if (0 === (int)$ret["date_end"]) {
            $ret["date_end"] = null;
        }


        return $ret;
    }


}