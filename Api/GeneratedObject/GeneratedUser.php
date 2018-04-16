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
class GeneratedUser extends TableCrudObject
{

    public function __construct()
    {
        parent::__construct();
        $this->table = "ek_user";
        $this->primaryKey = ['id'];
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    protected function getCreateData(array $data)
    {
        $base = [
			'id' => null,
			'email' => '',
			'pass' => '',
			'pseudo' => '',
			'company' => '',
			'first_name' => '',
			'last_name' => '',
			'date_creation' => '',
			'date_last_connection' => null,
			'mobile' => '',
			'phone' => '',
			'phone_prefix' => '',
			'newsletter' => 0,
			'birthday' => null,
			'active_hash' => '',
			'active' => 0,
			'user_group_id' => 0,
			'gender_id' => null,
		];
        $ret = array_replace($base, array_intersect_key($data, $base));

        if (0 === (int)$ret["gender_id"]) {
            $ret["gender_id"] = null;
        }
        if ("" === $ret["date_last_connection"]) {
            $ret["date_last_connection"] = null;
        }
        if ("" === $ret["birthday"]) {
            $ret["birthday"] = null;
        }


        return $ret;
    }


}