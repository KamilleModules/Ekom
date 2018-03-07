<?php


use Authenticate\SessionUser\SessionUser;
use Module\Ekom\Api\EkomApi;
use OnTheFlyForm\Helper\OffProtocolHelper;
use OnTheFlyForm\Provider\OnTheFlyFormProviderInterface;



switch ($doo) {
    case 'all':
        break;
    case 'user.saveAddress':
        // off protocol
        $out = [];
        if (SessionUser::isConnected()) {

            $userId = SessionUser::getValue("id");
            $sData = getArgument("data", true);
            parse_str($sData, $data);

            $userAddressLayer = EkomApi::inst()->userAddressLayer();

            $addressId = null;
            if (array_key_exists("address_id", $data)) {
                $addressId = $data["address_id"];
            }

            /**
             * @var $provider OnTheFlyFormProviderInterface
             */
            $provider = X::get("Core_OnTheFlyFormProvider");
            $form = $provider->getForm("Ekom", "UserAddress");
            $form->inject($data);

            if (true === $form->validate()) {

                $data = $form->getData();
                if (true === $userAddressLayer->createAddress($userId, $data, $addressId)) {
                    $data = $userAddressLayer->getUserAddresses($userId);
                    OffProtocolHelper::success($out, $form, $data);
                } else {
                    $form->setErrorMessage("An exception occurred, the address couldn't be created, please contact the webmaster.");
                    OffProtocolHelper::formError($out, $form);
                }
            } else {
                OffProtocolHelper::formError($out, $form);
            }
        } else {
            OffProtocolHelper::error($out, "the user is not connected");
        }
        break;
    default:
        break;
}