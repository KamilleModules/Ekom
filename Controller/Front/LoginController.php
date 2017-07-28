<?php


namespace Controller\Ekom\Front;


use Authenticate\SessionUser\SessionUser;
use Bat\UriTool;
use Controller\Ekom\EkomFrontController;
use FormModel\FormModelInterface;
use Kamille\Architecture\Response\Web\RedirectResponse;
use Kamille\Utils\Laws\Config\LawsConfig;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\E;

class LoginController extends EkomFrontController
{

    public function render()
    {

        $key = "Ekom_Front_LoginController_render";

        $model = [
            "formAction" => "",
            "formMethod" => "post",
            "errorForm" => "",
            "nameEmail" => "email",
            "namePass" => "pass",
            "nameKey" => "key",
            "valueEmail" => "",
            "valuePass" => "",
            "valueKey" => $key,
            "uriCreateAccount" => E::link("Ekom_createAccount"),
            "uriForgotPassword" => E::link("Ekom_forgotPassword"),
        ];


        if (array_key_exists($model['nameKey'], $_POST) && $key === $_POST[$model['nameKey']]) {


            $errorMsg = "The entry does not not exist in the database, or the password doesn't match";

            $model['valueEmail'] = $_POST[$model['nameEmail']];
            $model['valuePass'] = $_POST[$model['namePass']];
            $mail = $model['valueEmail'];
            if (false !== ($row = EkomApi::inst()->user()->readOne([
                    'where' => [
                        ["email", "=", $mail],
                    ],
                ]))
            ) {
                if ('1' === $row['active']) {
                    $hash = $row['pass'];
                    if (true === EkomApi::inst()->passwordLayer()->passwordVerify($model['valuePass'], $hash)) {


                        EkomApi::inst()->userLayer()->connectUser(['id' => $row['id']]);


                        return RedirectResponse::create(UriTool::getWebsiteAbsoluteUrl());

                    } else {
                        $model['errorForm'] = $errorMsg;
                    }
                } else {
                    $model['errorForm'] = "This user has been de-activated";
                }
            } else {
                $model['errorForm'] = $errorMsg;
            }
        }


        /**
         * @var $model FormModelInterface
         */

        return $this->renderByViewId("Ekom/login", LawsConfig::create()->replace([
            "widgets" => [
                'maincontent.login' => [
                    "conf" => $model,
                ],
            ],
        ]));
    }
}