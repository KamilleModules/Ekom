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
class GeneratedCategoryHasProductCard extends TableCrudObject
{

    public function __construct()
    {
        parent::__construct();
        $this->table = "kamille.ek_category_has_product_card";
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    protected function getCreateData(array $data)
    {
        $ret = array_replace([
			'category_id' => 0,
			'product_card_id' => 0,
		], $data);



        return $ret;
    }


}