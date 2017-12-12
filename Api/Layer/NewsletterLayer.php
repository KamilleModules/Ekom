<?php


namespace Module\Ekom\Api\Layer;


use Module\Ekom\Api\EkomApi;
use QuickPdo\QuickPdo;

class NewsletterLayer
{


    public static function isRegistered($email)
    {
        $res = QuickPdo::fetch("
select id from ek_newsletter
where email=:email        
        ", [
            "email" => $email,
        ], \PDO::FETCH_COLUMN);

        return (false !== $res);
    }


    public static function registerEmail($email)
    {
        return EkomApi::inst()->newsletter()->create([
            "email" => $email,
            "subscribe_date" => date("Y-m-d H:i:s"),
            "unsubscribe_date" => null,
            "active" => 1,
        ]);
    }

}