<?php


namespace Module\Ekom\Api\Layer;


use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;
use QuickPdo\QuickPdoStmtTool;

class NewsletterLayer
{


    public static function getNbNewSubscribers($dateStart = null, $dateEnd = null)
    {

        $q = "
select count(*) as count
from ek_newsletter
where unsubscribe_date is null
        ";

        $markers = [];
        QuickPdoStmtTool::addDateRangeToQuery($q, $markers, $dateStart, $dateEnd, "subscribe_date");

        return (int)QuickPdo::fetch($q, $markers, \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
    }


    public static function getNbTotalSubscribers($dateStart = null, $dateEnd = null)
    {
        return self::getNbNewSubscribers(null, $dateEnd);
    }


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
        $ip = $_SERVER["REMOTE_ADDR"]??"";
        $userId = E::getUserId(null);


        return EkomApi::inst()->newsletter()->create([
            "email" => $email,
            "subscribe_date" => date("Y-m-d H:i:s"),
            "unsubscribe_date" => null,
            "active" => 1,
            "user_id" => $userId,
            "ip" => $ip,
        ]);
    }

}