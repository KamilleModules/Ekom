<?php


namespace Module\Ekom\Api\Layer;


use Authenticate\SessionUser\SessionUser;
use Core\Services\X;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Carrier\Collection\CarrierCollectionInterface;

class CarrierLayer
{

    public function getCarrierGroups(){


        $coll = X::get("Ekom_getCarrierCollection");
        /**
         * @var $coll CarrierCollectionInterface
         */
        $carriers = $coll->all();


        $rejected= [];

        $shippingAddress = null;
        if(true===SessionUser::isConnected()){
            $userId = SessionUser::getValue('id');
            $shippingAddress = EkomApi::inst()->userLayer()->getPreferredShippingAddress($userId);
            az($shippingAddress);
        }
        $productInfos = 0;
        foreach($carriers as $name => $carrier){
            $carrier->handleOrder($productInfos, $shippingAddress, $rejected);
        }

    }

}
