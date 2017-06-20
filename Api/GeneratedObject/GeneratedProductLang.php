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
class GeneratedProductLang extends TableCrudObject
{

    public function __construct()
    {
        parent::__construct();
        $this->table = "ek_product_lang";
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    protected function getCreateData(array $data)
    {
        $ret = array_replace([
			'product_id' => 0,
			'lang_id' => 0,
			'label' => '',
			'description' => '',
			'meta_title' => '',
			'meta_description' => '',
			'meta_keywords' => '',
		], $data);



        return $ret;
    }


}