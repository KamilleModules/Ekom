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
class GeneratedSearchStat extends TableCrudObject
{

    public function __construct()
    {
        parent::__construct();
        $this->table = "ek_search_stat";
        $this->primaryKey = ['id'];
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    protected function getCreateData(array $data)
    {
        $base = [
			'id' => null,
			'expression' => '',
			'results' => 0,
			'date_added' => '',
			'user_id' => null,
		];
        $ret = array_replace($base, array_intersect_key($data, $base));

        if (0 === (int)$ret["user_id"]) {
            $ret["user_id"] = null;
        }


        return $ret;
    }


}