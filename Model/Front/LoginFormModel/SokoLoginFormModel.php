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

    public static function getFormModel(&$response = null)
    {


        //--------------------------------------------
        // PRE-FILLED VALUES
        //--------------------------------------------
        $email = "";
        $pass = "";
        $memorize = "";
        if (array_key_exists('ekom-login-email', $_COOKIE)) {
            $email = $_COOKIE['ekom-login-email'];
            if (array_key_exists('ekom-login-pass', $_COOKIE)) {
                $pass = $_COOKIE['ekom-login-pass'];
            }
            $memorize = "1";
        }


        //--------------------------------------------
        // FORM CREATION
        //--------------------------------------------
        /**
         * @todo-ling: add some validation rules (email)
         */
        $form = SokoForm::create()
            ->addControl(SokoInputControl::create()
                ->setName("email")
                ->setValue($email)
                ->setPlaceholder("Votre email")
            )
            ->addControl(SokoInputControl::create()
                ->setName("pass")
                ->setValue($pass)
                ->setPlaceholder("Mot de passe")
                ->setType("password")
            )
            ->addControl(SokoChoiceControl::create()
                ->setName("memorize")
                ->setChoices([
                    "1" => "Mémoriser mes informations sur cet ordinateur",
                ])

                ->setValue($memorize)
            );


        //--------------------------------------------
        // FORM HANDLING
        //--------------------------------------------
        $response = null;
        $form->process(function (array $context, SokoFormInterface $form) use (&$response) {


            $email = $context['email'];
            $pass = $context['pass'];
            $errorMsg = "The entry does not not exist in the database, or the password doesn't match";


            //--------------------------------------------
            // REMEMBER ME
            //--------------------------------------------
            if (array_key_exists('memorize', $context) && '1' === $context['memorize']) {
                $t = time() + 86400 * 365; // memorize one year
                setcookie("ekom-login-email", $email, $t);
                setcookie("ekom-login-pass", $pass, $t);
            } else {
                unset($_COOKIE['ekom-login-email']);
                unset($_COOKIE['ekom-login-pass']);
                setcookie('ekom-login-email', null, -1, '/');
                setcookie('ekom-login-pass', null, -1, '/');
            }


            if (false !== ($row = UserLayer::getUserInfoByEmail($email))) {
                if ('1' === $row['active']) {

                    $hash = $row['pass'];

                    if (true === EkomApi::inst()->passwordLayer()->passwordVerify($pass, $hash)) {

                        $userId = $row['id'];
                        //--------------------------------------------
                        // CONNECT THE USER
                        //--------------------------------------------
                        $data = ConnexionLayer::getConnexionDataByUserId($userId);
                        EkomApi::inst()->connexionLayer()->connect($data);
                        Hooks::call("Ekom_onUserConnectedAfter");


                        //--------------------------------------------
                        // FOR THIS FORM, WE ALSO WOULD LIKE TO REDIRECT THE USER
                        //--------------------------------------------
                        $target = E::pickUpReferer();
                        $hashFragment = null;
                        if (array_key_exists('hash', $_GET)) {
                            $hashFragment = $_GET['hash'];
                        }
                        if (null !== $hashFragment) {
                            $target .= '#' . $hashFragment;
                        }
                        $response = RedirectResponse::create($target);


                    } else {
                        $form->addNotification($errorMsg, "warning");
                    }
                } else {
                    $form->addNotification("This user has been de-activated", "warning");
                }
            } else {
                $form->addNotification($errorMsg, "warning");
            }

        });

        $model = $form->getModel();

        $model["uriCreateAccount"] = E::link("Ekom_createAccount");
        $model["uriForgotPassword"] = E::link("Ekom_forgotPassword");

        //--------------------------------------------
        // DECORATE MODEL DEPENDING ON THE THEME
        //--------------------------------------------
        Hooks::call("Ekom_Theme_decorate_SokoLoginFormModel", $model);


        return $model;

    }
}