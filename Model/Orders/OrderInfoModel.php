<?php


namespace Module\Ekom\Model\Orders;


use Module\Ekom\Api\Layer\OrderLayer;
use Module\Ekom\Api\Layer\SellerLayer;

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
            $firstName = ucfirst(strtolower($userInfo['first_name']));
            $lastName = ucfirst(strtolower($userInfo['last_name']));
            $company = (array_key_exists("company", $userInfo) && $userInfo['company']) ? ucfirst(strtolower($userInfo['company'])) : '';


            if ($firstName) {
                $userRepr .= $firstName;
            }
            if ($lastName) {
                if ($userRepr) {
                    $userRepr .= ' ';
                }
                $userRepr .= $lastName;
            }
            if ($company) {
                $hasName = false;
                if ($userRepr) {
                    $hasName = true;
                }

                if (true === $hasName) {
                    $userRepr .= ' (';
                }
                $userRepr .= $company;
                if (true === $hasName) {
                    $userRepr .= ')';
                }
            }
            $ret['user_representation'] = $userRepr;
            $ret['page_title'] = "Commande " . $ret['reference'] . " de $userRepr";
            $ret['billing_address'] = self::formatAddress($ret['billing_address']);
            $ret['shipping_address'] = self::formatAddress($ret['shipping_address']);
            $ret['sellerName2Label'] = SellerLayer::getName2LabelList();
            $ret['tracker_identifiers'] = OrderLayer::getTrackerIdentifiersByOrderId($id);

        }
        return $ret;
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    protected static function formatAddress(array $address)
    {

        if (
            false === array_key_exists("libelle", $address) &&
            array_key_exists('fName', $address)
        ) {
            $address['libelle'] = $address['fName'];
        }


        $phoneFormatted = $address['phone'];
        if ($phoneFormatted && $address['phone_prefix']) {
            $phoneFormatted = "(+$address[phone_prefix]) " . $phoneFormatted;
        }
        $address['phone_formatted'] = $phoneFormatted;
        return $address;
    }
}