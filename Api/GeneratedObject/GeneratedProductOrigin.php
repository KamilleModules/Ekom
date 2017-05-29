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
class GeneratedProductOrigin extends TableCrudObject
{

    public function __construct()
    {
        parent::__construct();
        $this->table = "kamille.ek_product_origin";
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    protected function getCreateData(array $data)
    {
        $ret = array_replace([
			'type' => '',
			'value' => '',
			'image' => null,
		], $data);


        if (0 === (int)$ret["image"]) {
            $ret["image"] = null;
        }

        return $ret;
    }


}