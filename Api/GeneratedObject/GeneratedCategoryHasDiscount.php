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
class GeneratedCategoryHasDiscount extends TableCrudObject
{

    public function __construct()
    {
        parent::__construct();
        $this->table = "ek_category_has_discount";
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    protected function getCreateData(array $data)
    {
        $base = [
			'category_id' => 0,
			'discount_id' => 0,
			'order_phase' => 0,
			'active' => 0,
		];
        $ret = array_replace($base, array_intersect_key($data, $base));



        return $ret;
    }


}