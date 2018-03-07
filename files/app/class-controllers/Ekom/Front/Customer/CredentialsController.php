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
use Module\Ekom\SokoForm\EkomSokoForm;
use Module\Ekom\Utils\E;
use OnTheFlyForm\OnTheFlyForm;
use SokoForm\Control\SokoInputControl;
use SokoForm\Form\SokoForm;
use SokoForm\Form\SokoFormInterface;
use SokoForm\ValidationRule\SokoNotEmptyValidationRule;
use SokoForm\ValidationRule\SokoValidationRule;

class CredentialsController extends CustomerController
{

    protected function prepareClaws()
    {
        parent::prepareClaws();

        $userId = E::getUserId();


        $form = EkomSokoForm::create()
            ->addControl(SokoInputControl::create()
                ->setLabel("Mot de passe actuel")
                ->setName("current_pass")
            )
            ->addControl(SokoInputControl::create()
                ->setLabel("Nouveau mot de passe")
                ->setName("pass")
            )
            ->addControl(SokoInputControl::create()
                ->setLabel("Nouveau mot de passe (deuxième fois)")
                ->setName("pass_confirm")
            );


        $form
            ->addValidationRule("current_pass", SokoNotEmptyValidationRule::create())
            ->addValidationRule("pass", SokoNotEmptyValidationRule::create())
            ->addValidationRule("pass_confirm", SokoNotEmptyValidationRule::create());


        if (false !== ($accountInfo = EkomApi::inst()->userLayer()->getAccountInfo($userId))) {

            $passLayer = EkomApi::inst()->passwordLayer();
            $form->process(function (array $data, SokoFormInterface $form) use ($userId, $accountInfo, $passLayer) {
                $hash = $accountInfo['pass'];
                $currentPassword = $data['current_pass'];
                $newPassword = $data['pass'];
                $newPassword2 = $data['pass_confirm'];


                if ($newPassword === $newPassword2) {


                    if (true === $passLayer->passwordVerify($currentPassword, $hash)) {

                        $newHash = $passLayer->passEncrypt($newPassword);
                        EkomApi::inst()->user()->update([
                            'pass' => $newHash,
                        ], [
                            'id' => $userId,
                        ]);
                        $form->addNotification("Le mot de passe a bien été mis à jour", "success");

                    } else {
                        $form->addNotification("Le mot de passe actuel n'est pas valide", "error");
                    }
                } else {
                    $form->addNotification("Les 2 mots de passe ne sont pas identiques", "error");
                }
            });

        } else {
            XLog::error("The user with id $userId doesn't have an account");
            $form->addNotification("The user doesn't have an account", "error");
        }


        $model = $form->getModel();


        $this->getClaws()
            ->setWidget("maincontent.credentials", ClawsWidget::create()
                ->setTemplate("Ekom/Customer/Credentials/default")
                ->setConf([
                    'form' => $model,
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