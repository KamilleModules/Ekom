<?php


namespace Module\Ekom\Model\Front\LoginFormModel;



use Core\Services\Hooks;
use Kamille\Architecture\Response\Web\RedirectResponse;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Layer\ConnexionLayer;
use Module\Ekom\Utils\E;

class OffLoginFormModel
{


    /**
     *
     *
     *
     * @param null $response , if not null, the Response where the user should be redirected.
     *                  This also can be interpreted as the signal that the form has been
     *                  successfully posted.
     * @return array, an OnTheFlyForm model
     */
    public static function handleLoginForm(&$response = null)
    {

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