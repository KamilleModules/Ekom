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
class GeneratedAction extends TableCrudObject
{

    public function __construct()
    {
        parent::__construct();
        $this->table = "kamille.ek_action";
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    protected function getCreateData(array $data)
    {
        $ret = array_replace([
			'source' => '',
			'source2' => null,
			'operator' => '',
			'target' => '',
			'target2' => null,
		], $data);


        if (0 === (int)$ret["source2"]) {
            $ret["source2"] = null;
        }
        if (0 === (int)$ret["target2"]) {
            $ret["target2"] = null;
        }

        return $ret;
    }


}