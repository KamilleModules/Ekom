<?php


namespace Module\Ekom\Model\Front;


use Module\Ekom\Model\EkomModel;
use Module\Ekom\Session\EkomSession;

class CreateAccountSuccessModel extends EkomModel
{


    public function getModel(array $context)
    {

        $email = (array_key_exists("email", $context)) ? $context['email'] : null;
        return [
            "email" => $email,
        ];
    }


}