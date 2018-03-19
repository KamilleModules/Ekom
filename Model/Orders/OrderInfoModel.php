<?php


namespace Module\Ekom\Model\Orders;


use Module\Ekom\Api\Layer\OrderLayer;

class OrderInfoModel
{


    public static function getModelByOrderId($id)
    {
        $ret = OrderLayer::getOrderInfo($id);
        if (false !== $ret) {


            //--------------------------------------------
            // PAGE TITLE
            //--------------------------------------------
            // user representation
            $userRepr = '';
            $userInfo = $ret['user_info'];
            if ($userInfo['first_name']) {
                $userRepr .= ucfirst(strtolower($userInfo['first_name']));
            }
            if ($userInfo['last_name']) {
                if ($userRepr) {
                    $userRepr .= ' ';
                }
                $userRepr .= ucfirst(strtolower($userInfo['last_name']));
            }
            if (array_key_exists("company", $userInfo) && $userInfo['company']) {
                $hasName = false;
                if ($userRepr) {
                    $hasName = true;
                }

                if (true === $hasName) {
                    $userRepr .= ' (';
                }
                $userRepr .= ucfirst(strtolower($userInfo['company']));
                if (true === $hasName) {
                    $userRepr .= ')';
                }
            }
            $ret['user_representation'] = $userRepr;
            $ret['page_title'] = "Commande " . $ret['reference'] . " de $userRepr";


        }
        return $ret;
    }

}