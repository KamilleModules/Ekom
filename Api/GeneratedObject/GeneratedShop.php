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
class GeneratedShop extends TableCrudObject
{

    public function __construct()
    {
        parent::__construct();
        $this->table = "ek_shop";
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
			'host' => '',
			'lang_id' => null,
			'currency_id' => 0,
			'timezone_id' => 0,
		];
        $ret = array_replace($base, array_intersect_key($data, $base));

        if (0 === (int)$ret["lang_id"]) {
            $ret["lang_id"] = null;
        }


        return $ret;
    }


}