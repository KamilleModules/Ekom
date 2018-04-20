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
class GeneratedProduct extends TableCrudObject
{

    public function __construct()
    {
        parent::__construct();
        $this->table = "ek_product";
        $this->primaryKey = ['id'];
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    protected function getCreateData(array $data)
    {
        $base = [
			'id' => null,
			'product_card_id' => 0,
			'seller_id' => 0,
			'manufacturer_id' => null,
			'label' => '',
			'description' => '',
			'slug' => '',
			'meta_title' => '',
			'meta_description' => '',
			'meta_keywords' => '',
			'wholesale_price' => '',
			'out_of_stock_text' => '',
			'active' => 0,
			'_discount_badge' => '',
			'_popularity' => '',
			'codes' => '',
			'ean' => '',
			'height' => null,
			'depth' => null,
			'weight' => '',
			'width' => null,
		];
        $ret = array_replace($base, array_intersect_key($data, $base));

        if (0 === (int)$ret["manufacturer_id"]) {
            $ret["manufacturer_id"] = null;
        }
        if ("" === $ret["height"]) {
            $ret["height"] = null;
        }
        if ("" === $ret["depth"]) {
            $ret["depth"] = null;
        }
        if ("" === $ret["width"]) {
            $ret["width"] = null;
        }


        return $ret;
    }


}