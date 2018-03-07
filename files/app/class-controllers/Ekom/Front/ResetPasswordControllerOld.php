<?php


namespace Controller\Ekom\Front;


use Authenticate\SessionUser\SessionUser;
use Controller\Ekom\EkomFrontController;
use Kamille\Architecture\Response\Web\RedirectResponse;
use Kamille\Services\XConfig;
use Kamille\Services\XLog;
use Kamille\Utils\Claws\ClawsWidget;
use Kamille\Utils\Laws\Config\LawsConfig;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Session\EkomSession;
use Module\Ekom\Utils\E;

class ResetPasswordControllerOld extends EkomFrontController
{



    public function render()
    {

        $key = "Ekom_Front_ResetPasswordController_render";
        $model = [
            //
            "email" => "",
            "formAction" => "",
            "formMethod" => "post",
            "errorForm" => "",

            "nameKey" => "key",
            "namePass" => "pass",
            "namePassConfirm" => "pass-confirm",

            "valueKey" => $key,
            "valuePass" => "",
            "valuePassConfirm" => "",
        ];


        if (array_key_exists('token', $_GET)) {
            $token = $_GET['token'];

            /**
             * ["id"] => string(1) "1"
             * ["user_id"] => string(1) "1"
             * ["date_created"] => string(19) "2017-07-15 15:24:30"
             * ["code"] => string(32) "23e3e4d5d4e67091fd06dc6eff8272a9"
             * ["date_used"] => NULL
             */
            $validCodeInfo = EkomApi::inst()->passwordLayer()->getCodeValidInfo($token);
            if (false !== $validCodeInfo) {


                $model['email'] = $validCodeInfo['email'];


                if (array_key_exists($model['nameKey'], $_POST) && $key === $_POST[$model['nameKey']]) {


                    $valuePass = $_POST[$model['namePass']];
                    $valuePassConfirm = $_POST[$model['namePassConfirm']];


                    $model['valuePass'] = $valuePass;
                    $model['valuePassConfirm'] = $valuePassConfirm;


                    if ($valuePass === $valuePassConfirm) {


                        $userId = $validCodeInfo['user_id'];
                        EkomApi::inst()->user()->update([
                            "pass" => EkomApi::inst()->passwordLayer()->passEncrypt($valuePass),
                        ], [
                            "id" => $userId,
                        ]);
                        EkomApi::inst()->passwordLayer()->useCode($validCodeInfo['id']);


                        $link = E::link("Ekom_customerDashboard", [], true);


                        // connect the user
                        EkomApi::inst()->userLayer()->connectUser(['id' => $validCodeInfo['user_id']]);

                        return RedirectResponse::create($link);


                    } else {
                        $model['errorForm'] = "The two passwords must match";
                    }


                }
            } else {
                $model['errorForm'] = "Invalid token";
            }
        } else {
            $model['errorForm'] = "token not found";
        }


        return $this->renderByViewId("Ekom/resetPassword", LawsConfig::create()->replace([
            'widgets' => [
                'maincontent.resetPassword' => [
                    "conf" => $model,
                ],
            ],
        ]));
    }
}