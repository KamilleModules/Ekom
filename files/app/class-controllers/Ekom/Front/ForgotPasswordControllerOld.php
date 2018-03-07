<?php


namespace Controller\Ekom\Front;


use Controller\Ekom\EkomFrontController;
use Kamille\Architecture\Response\Web\RedirectResponse;
use Kamille\Services\XConfig;
use Kamille\Services\XLog;
use Kamille\Utils\Claws\ClawsWidget;
use Kamille\Utils\Laws\Config\LawsConfig;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Session\EkomSession;
use Module\Ekom\Utils\E;

class ForgotPasswordControllerOld extends EkomFrontController
{


    public function render()
    {
        parent::prepareClaws();
        $key = "Ekom_Front_ForgotPasswordController_render";


        $model = [
            "mailIsSent" => false,
            "formAction" => "",
            "formMethod" => "post",
            "errorForm" => "",
            "nameKey" => "key",
            "nameEmail" => "email",
            "valueKey" => $key,
            "valueEmail" => "",
        ];


        if (
            array_key_exists($model['nameKey'], $_POST) &&
            $key === $_POST[$model['nameKey']]
        ) {


            $errorMsg = "Cet utilisateur n'existe pas dans notre base de données";

            $model['valueEmail'] = $_POST[$model['nameEmail']];
            $mail = $model['valueEmail'];

            if (false !== ($row = EkomApi::inst()->user()->readOne([
                    'where' => [
                        ["email", "=", $mail],
                    ],
                ]))
            ) {
                if ('1' === $row['active']) {


                    $code = EkomApi::inst()->passwordLayer()->createCodeByUser($row['id']);


//                    $reset_link = 0;

                    $res = E::sendMail("passwordRecovery", [
                        'to' => $mail,
                        'subject' => '{siteName}: Renvoi de mot de passe',
                        'commonVars' => [
                            'lastName' => $row['last_name'],
                            'firstName' => $row['first_name'],
                            'uriResetPassword' => E::link("Ekom_resetPassword", [], true) . "?token=$code",
                            'company' => XConfig::get("Application.site.name"),
                        ],
                    ]);

                    if (true === $res) {
                        EkomSession::set("ForgotPasswordController.email", $mail);
                        $link = E::link("Ekom_forgotPasswordSuccess", [], true);
                        return RedirectResponse::create($link);
                    } else {
                        XLog::error("[Ekom module] - ForgotPasswordController: sending mail to $mail failed");
                        $model['errorForm'] = "An error occurred with the sending of the e-mail. Sorry for the convenience. We are working on the issue right now";
                    }


                } else {
                    $model['errorForm'] = "This user has been de-activated";
                }
            } else {
                $model['errorForm'] = $errorMsg;
            }
        }


        $this->getClaws()->setLayout("sandwich_1c/default")
            ->setWidget("maincontent.forgotPassword", ClawsWidget::create()
                ->setTemplate("Ekom/ForgotPassword/default")
                ->setConf($model)
            );

        return $this->doRenderClaws();
    }

    public function renderSuccess()
    {
        $email = EkomSession::get("ForgotPasswordController.email");

        parent::prepareClaws();
        $this->getClaws()->setLayout("sandwich_1c/default")
            ->setWidget("maincontent.forgotPasswordSuccess", ClawsWidget::create()
                ->setTemplate("Ekom/ForgotPassword/default-success")
                ->setConf([
                    'email' => $email,
                ])
            );

        return $this->doRenderClaws();
    }
}