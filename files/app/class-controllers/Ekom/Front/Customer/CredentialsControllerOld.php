<?php


namespace Controller\Ekom\Front\Customer;


use Controller\Ekom\Front\CustomerController;
use Core\Services\A;
use Kamille\Services\XLog;
use Kamille\Utils\Claws\ClawsWidget;
use Kamille\Utils\Laws\Config\LawsConfig;
use Module\Ekom\AddressListFormatter\AddressListFormatter;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Exception\EkomException;
use Module\Ekom\Utils\E;
use OnTheFlyForm\OnTheFlyForm;

class CredentialsControllerOld extends CustomerController
{

    protected function prepareClaws()
    {
        parent::prepareClaws();

        $userId = E::getUserId();


        //--------------------------------------------
        // @ON THE FLY FORM DOC
        //--------------------------------------------
        $form = A::getOnTheFlyForm("Ekom:UserCredentials");
        if (false !== ($accountInfo = EkomApi::inst()->userLayer()->getAccountInfo($userId))) {

            $passLayer = EkomApi::inst()->passwordLayer();

            if (true === $form->isPosted()) {
                $form->inject($_POST);
                if (true === $form->validate()) {

                    $data = $form->getData();

                    $hash = $accountInfo['pass'];
                    $currentPassword = $data['current_pass'];
                    $newPassword = $data['pass'];

                    if (true === $passLayer->passwordVerify($currentPassword, $hash)) {

                        $newHash = $passLayer->passEncrypt($newPassword);
                        EkomApi::inst()->user()->update([
                            'pass' => $newHash,
                        ], [
                            'id' => $userId,
                        ]);
                        $form->setSuccessMessage("The password has been successfully updated!");

                    } else {
                        $form->setErrorMessage("The current password is not correct");
                    }
                }
            } else {
                $defaultValues = [
                    // empty
                ];

                $form->inject($defaultValues, true);
            }


        } else {
            XLog::error("The user with id $userId doesn't have an account");
            $form->setErrorMessage("The user doesn't have an account");
        }


        $model = $form->getModel();
        $this->getClaws()
            ->setWidget("maincontent.credentials", ClawsWidget::create()
                ->setTemplate("Ekom/Customer/Credentials/default")
                ->setConf([
                    'm:formModel' => $model,
                ])
            );
    }


    /**
     * @deprecated
     */
//    protected function connectedRender()
//    {
//
//        $userId = E::getUserId();
//
//
//        //--------------------------------------------
//        // @ON THE FLY FORM DOC
//        //--------------------------------------------
//        $form = A::getOnTheFlyForm("Ekom:UserCredentials");
//        if (false !== ($accountInfo = EkomApi::inst()->userLayer()->getAccountInfo($userId))) {
//
//            $passLayer = EkomApi::inst()->passwordLayer();
//
//            if (true === $form->isPosted()) {
//                $form->inject($_POST);
//                if (true === $form->validate()) {
//
//                    $data = $form->getData();
//
//                    $hash = $accountInfo['pass'];
//                    $currentPassword = $data['current_pass'];
//                    $newPassword = $data['pass'];
//
//                    if (true === $passLayer->passwordVerify($currentPassword, $hash)) {
//
//                        $newHash = $passLayer->passEncrypt($newPassword);
//                        EkomApi::inst()->user()->update([
//                            'pass' => $newHash,
//                        ], [
//                            'id' => $userId,
//                        ]);
//                        $form->setSuccessMessage("The password has been successfully updated!");
//
//                    } else {
//                        $form->setErrorMessage("The current password is not correct");
//                    }
//                }
//            } else {
//                $defaultValues = [
//                    // empty
//                ];
//
//                $form->inject($defaultValues, true);
//            }
//
//
//        } else {
//            XLog::error("The user with id $userId doesn't have an account");
//            $form->setErrorMessage("The user doesn't have an account");
//        }
//
//
//        //--------------------------------------------
//        // RENDERING
//        //--------------------------------------------
//        $model = $form->getModel();
//        return $this->renderByViewId("Ekom/customer/credentials", LawsConfig::create()->replace([
//            'widgets' => [
//                'maincontent.credentials' => [
//                    'conf' => [
//                        'm:formModel' => $model,
//                    ],
//                ],
//            ],
//        ]));
//    }
}