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
class GeneratedProductHasTaxRule extends TableCrudObject
{

    public function __construct()
    {
        parent::__construct();
        $this->table = "kamille.ek_product_has_tax_rule";
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    protected function getCreateData(array $data)
    {
        $ret = array_replace([
			'product_id' => 0,
			'tax_rule_id' => 0,
		], $data);



        return $ret;
    }


}