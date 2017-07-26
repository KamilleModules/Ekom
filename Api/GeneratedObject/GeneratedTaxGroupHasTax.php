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
class GeneratedTaxGroupHasTax extends TableCrudObject
{

    public function __construct()
    {
        parent::__construct();
        $this->table = "ek_tax_group_has_tax";
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    protected function getCreateData(array $data)
    {
        $base = [
			'tax_group_id' => 0,
			'tax_id' => 0,
			'order' => 0,
			'mode' => '',
		];
        $ret = array_replace($base, array_intersect_key($data, $base));



        return $ret;
    }


}