<?php


namespace Module\Ekom\Model\Front\LoginFormModel;


use Core\Services\Hooks;
use Kamille\Architecture\Response\Web\RedirectResponse;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Layer\ConnexionLayer;
use Module\Ekom\Api\Layer\UserLayer;
use Module\Ekom\Utils\E;
use SokoForm\Control\SokoChoiceControl;
use SokoForm\Control\SokoControlInterface;
use SokoForm\Control\SokoInputControl;
use SokoForm\Form\SokoForm;
use SokoForm\Form\SokoFormInterface;
use SokoForm\ValidationRule\SokoValidationRule;

class SokoLoginFormModel
{

    public static function handleLoginForm(&$response = null)
    {


        $form = SokoForm::create()
            ->addControl(SokoInputControl::create()
                ->setName("email")
            )
            ->addControl(SokoInputControl::create()
                ->setName("pass")
            )
            ->addControl(SokoChoiceControl::create()
                ->setName("memorize")
            )
            ->addValidationRule("email", SokoValidationRule::create()
                /**
                 * If value is null, it means that the control was not posted.
                 * This behaviour might be useful in the case of checkboxes.
                 */
                ->setValidationFunction(function ($value, array &$preferences, &$error = null, SokoFormInterface $form, SokoControlInterface $control, array $context) {


                    $value = (string)$value;
                    if (strlen($value) < $preferences['minChar']) {
                        $errors[] = "This field requires at least {minChar} characters";
                        return false;
                    }
                    return true;
                })
            );


        $key = "Ekom_Front_LoginController_render";
        $hashFragment = null;
        if (array_key_exists('hash', $_GET)) {
            $hashFragment = $_GET['hash'];
        }

        $email = "";
        $pass = "";
        $checked = "";
        if (array_key_exists('ekom-login-email', $_COOKIE)) {
            $email = $_COOKIE['ekom-login-email'];
            if (array_key_exists('ekom-login-pass', $_COOKIE)) {
                $pass = $_COOKIE['ekom-login-pass'];
            }
            $checked = "checked";
        }

        $model = [
            "formAction" => "",
            "formMethod" => "post",
            "errorForm" => "",

            "nameEmail" => "email",
            "namePass" => "pass",

            "nameKey" => "ekom-login-key",

            "nameMemorize" => "memorize",

            "valueEmail" => $email,
            "valuePass" => $pass,
            "valueKey" => $key,
            "checkedMemorize" => $checked,
            "uriCreateAccount" => E::link("Ekom_createAccount"),
            "uriForgotPassword" => E::link("Ekom_forgotPassword"),
        ];


        if (array_key_exists($model['nameKey'], $_POST) && $key === $_POST[$model['nameKey']]) {


            $errorMsg = "The entry does not not exist in the database, or the password doesn't match";

            $model['valueEmail'] = $_POST[$model['nameEmail']];
            $model['valuePass'] = $_POST[$model['namePass']];
            $model['checkedMemorize'] = (array_key_exists($model['nameMemorize'], $_POST)) ? 'checked' : '';


            if ('checked' === $model['checkedMemorize']) {
                $t = time() + 86400 * 356;
                setcookie("ekom-login-email", $model['valueEmail'], $t);
                setcookie("ekom-login-pass", $model['valuePass'], $t);
            }

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


                        $userId = $row['id'];
                        $data = ConnexionLayer::getConnexionDataByUserId($userId);


                        EkomApi::inst()->connexionLayer()->connect($data);


                        Hooks::call("Ekom_onUserConnectedAfter");


                        $target = E::pickUpReferer();

                        if (null !== $hashFragment) {
                            $target .= '#' . $hashFragment;
                        }
                        $response = RedirectResponse::create($target);


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
        return $model;
    }
}