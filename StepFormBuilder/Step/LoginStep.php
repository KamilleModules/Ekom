<?php


namespace Module\Ekom\StepFormBuilder\Step;


use Core\Services\A;
use Core\Services\Hooks;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\E;
use StepFormBuilder\Step\OnTheFlyFormStep;

class LoginStep extends OnTheFlyFormStep
{
    public function __construct()
    {
        parent::__construct();
        $this->setForm(A::getOnTheFlyForm("Ekom:CheckoutLogin"));
    }

    public function isPosted()
    {
        $ret = parent::isPosted();
        if (true === $ret) {
            $model = $this->getForm()->getModel();
            if ('checked' === $model['checkedMemorize']) {
                $t = time() + 86400 * 356;
                setcookie("ekom-login-email", $model['valueEmail'], $t);
                setcookie("ekom-login-pass", $model['valuePass'], $t);
            }
        }
        return $ret;
    }

    public function getModel(array $defaultValues)
    {
        $checked = "";
        if (array_key_exists('ekom-login-email', $_COOKIE)) {
            $email = $_COOKIE['ekom-login-email'];
            if (array_key_exists('ekom-login-pass', $_COOKIE)) {
                $pass = $_COOKIE['ekom-login-pass'];
            }
            $checked = "checked";
        }
        return parent::getModel($defaultValues);
    }


    protected function onSuccessfulValidateAfter(array $data, &$ret)
    {
        $errorMsg = "The entry does not not exist in the database, or the password doesn't match";
        $email = $data['email'];
        $pass = $data['pass'];


        if (false !== ($row = EkomApi::inst()->user()->readOne([
                'where' => [
                    ["email", "=", $email],
                ],
            ]))
        ) {
            if ('1' === $row['active']) {
                $hash = $row['pass'];

                if (true === EkomApi::inst()->passwordLayer()->passwordVerify($pass, $hash)) {


                    EkomApi::inst()->connexionLayer()->connect(['id' => $row['id']]);
                    Hooks::call("Ekom_onUserConnectedAfter");

                } else {
                    $this->getForm()->setErrorMessage($errorMsg);
                    $ret = false;
                }
            } else {
                $this->getForm()->setErrorMessage("This user has been de-activated");
                $ret = false;
            }
        } else {
            $this->getForm()->setErrorMessage($errorMsg);
            $ret = false;
        }


    }


}
