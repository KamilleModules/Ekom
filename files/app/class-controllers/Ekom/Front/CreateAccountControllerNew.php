<?php


namespace Controller\Ekom\Front;


use Bat\PdoTool;
use Bat\SessionTool;
use Bat\UriTool;
use Controller\Ekom\EkomFrontController;
use FormTools\Validation\OnTheFlyFormValidator;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Architecture\Response\Web\RedirectResponse;
use Kamille\Services\XConfig;
use Kamille\Services\XLog;
use Kamille\Utils\Claws\ClawsWidget;
use Kamille\Utils\Laws\Config\LawsConfig;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdoExceptionTool;

class CreateAccountControllerNew extends EkomFrontController
{


    protected function prepareClaws()
    {
        parent::prepareClaws();


        $this->setSessionReferer = false;
        $tpl = "leaderfit";


        if (array_key_exists('error', $_GET)) {
            $tpl = "error";
            $model = [
                'errorType' => $_GET['error'],
            ];
        } elseif (array_key_exists('success', $_GET)) {
            $tpl = "success";
            $type = "created";
            $email = "";
            if (array_key_exists('mail', $_GET)) {
                $email = $_GET['mail'];
                $type = "mailConfirmation";
            }

            $model = [
                "type" => $type,
                "email" => $email,
            ];
        } else {


            $key = "Ekom_Front_CreateAccountController_render";
            $model = [
                "formAction" => "",
                "formMethod" => "post",
                "nameEmail" => "email",
                "namePass" => "pass",
                "namePass2" => "pass2",
                "nameKey" => "key",
                "nameNewsletter" => "newsletter",
                "valueEmail" => "",
                "valuePass" => "",
                "valuePass2" => "",
                "valueKey" => $key,
                "checkedNewsletter" => "",
                //
                "errorEmail" => "",
                "errorPass" => "",
                "errorPass2" => "",
            ];


            if (array_key_exists($model['nameKey'], $_POST) && $key === $_POST[$model['nameKey']]) {
                $model['valueEmail'] = $_POST[$model['nameEmail']];
                $model['valuePass'] = $_POST[$model['namePass']];
                $model['valuePass2'] = $_POST[$model['namePass2']];
                $model['checkedNewsletter'] = (array_key_exists($model['nameNewsletter'], $_POST)) ? 'checked' : '';


                $validator = OnTheFlyFormValidator::create();
                if (true === $validator->validate([
                        'email' => ['required', 'email'],
                        'pass' => ['required', "min:3"],
                        'pass2' => ['required', 'sameAs:pass'],
                    ], $model)
                ) {

                    EkomApi::inst()->initWebContext();
                    try {


                        EkomApi::inst()->user()->create([
                            'shop_id' => ApplicationRegistry::get("ekom.shop_id"),
                            'email' => $model['valueEmail'],
                            'pass' => EkomApi::inst()->passwordLayer()->passEncrypt($model["valuePass"]),
                            'date_creation' => date('Y-m-d H:i:s'),
                            'mobile' => "",
                            'phone' => "",
                            'newsletter' => (int)($model['checkedNewsletter']),
                            'active' => "1", // todo: different modes...
                        ]);

                        $params = [
                            "success" => "1",
                        ];
                        if (true === E::sendMail("accountCreated", [
                                "to" => $model['valueEmail'],
                            ])
                        ) {
                            /**
                             * if mail exists, means the user needs to check her mail
                             */
                            $params['mail'] = $model['valueEmail'];
                        }

                        $uri = UriTool::uri(null, $params, true, true);
                        return RedirectResponse::create($uri);


                    } catch (\Exception $e) {
                        XLog::error("$e");
                        $errType = "exception";
                        if (true === QuickPdoExceptionTool::isDuplicateEntry($e)) {
                            $errType = 'duplicate';
                        }
                        $uri = UriTool::uri(null, ['error' => $errType], true, true);
                        return RedirectResponse::create($uri);
                    }
                }

            }
        }

//        az($model);

        $this->getClaws()
            ->setLayout("sandwich_1c/default")
            ->setWidget("maincontent.createAccount", ClawsWidget::create()
                ->setTemplate("Ekom/CreateAccount/$tpl")
                ->setConf($model)
            );
    }
}