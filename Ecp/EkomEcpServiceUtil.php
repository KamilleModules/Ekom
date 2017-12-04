<?php


namespace Module\Ekom\Ecp;


use Core\Services\Hooks;
use Ecp\EcpServiceUtil;
use Ecp\Exception\EcpInvalidArgumentException;
use Ecp\Exception\EcpUserMessageException;
use Ecp\Output\EcpOutputInterface;
use Kamille\Services\XLog;
use Module\Ekom\Exception\EkomUserMessageException;

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

    protected static function doExecuteProcess($process, $action, $intent, EcpOutputInterface $ecpOutput)
    {
        try {
            return parent::doExecuteProcess($process, $action, $intent, $ecpOutput);
        } catch (EkomUserMessageException $e) {
            $type = $e->getType();
            if ('success' === $type) {
                $ecpOutput->success($e->getMessage());
            } else {
                throw new EcpUserMessageException($e->getMessage(), $e->getCode(), $e);
            }
        }
    }


}