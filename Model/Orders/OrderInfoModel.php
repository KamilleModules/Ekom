<?php


namespace Module\Ekom\Model\Orders;


use Core\Services\A;
use Module\Ekom\Api\Layer\OrderLayer;
use Module\Ekom\Api\Layer\SellerLayer;
use Module\Ekom\Exception\EkomException;

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
            if (false === $userInfo) {
                throw new EkomException("Commande invalide");
            }
            $ret['user_link'] = A::link("Ekom_Users_User_Info") . "?id=" . $userInfo['id'];
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
            $ret['userInfo'] = $userInfo;
            $ret['user_representation'] = $userRepr;
            $ret['page_title'] = "Commande " . $ret['reference'] . " de $userRepr";
            $ret['billing_address'] = self::formatAddress($ret['billing_address']);
            $ret['shipping_address'] = self::formatAddress($ret['shipping_address']);
            $ret['sellerName2Label'] = SellerLayer::getName2LabelList();

            $trackerIdentifiers = OrderLayer::getTrackerIdentifiersByOrderId($id);
            $orderDetails = $ret['order_details'];
            if (array_key_exists("carrier_tracking_number", $orderDetails) && $orderDetails['carrier_tracking_number']) {
                $trackerIdentifiers[] = $orderDetails['carrier_tracking_number'];
            }
            $ret['tracker_identifiers'] = $trackerIdentifiers;


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
//        if ($phoneFormatted && $address['phone_prefix']) {
//            $phoneFormatted = "(+$address[phone_prefix]) " . $phoneFormatted;
//        }
        $address['phone_formatted'] = $phoneFormatted;
        return $address;
    }
}