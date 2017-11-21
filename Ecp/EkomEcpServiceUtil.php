<?php


namespace Module\Ekom\Ecp;


use Core\Services\Hooks;
use Ecp\EcpServiceUtil;
use Ecp\Exception\EcpInvalidArgumentException;
use Kamille\Services\XLog;

class EkomEcpServiceUtil extends EcpServiceUtil
{
    protected static function onInvalidArgumentAfter(EcpInvalidArgumentException $e)
    {
        // do you want to log those messages?
        Hooks::call("Ekom_Ecp_logInvalidArgumentException", $e);
    }

    protected static function onErrorAfter(\Exception $e)
    {
        XLog::error("[Ekom module] - ecp - $e");
    }
}