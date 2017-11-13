<?php


namespace Module\Ekom\Utils\Service;


use Core\Services\Hooks;
use Kamille\Services\XLog;
use Module\Ekom\Exception\EkomInvalidArgumentException;
use Module\Ekom\Exception\EkomUserMessageException;

class EcpServiceUtil
{


    /**
     *
     * Please read "Create your own ECP service, preserve the harmony" section from the
     * doc/apis/ekom-service-api.md document first.
     *
     *
     * Execute a process.
     *
     * - array function process ( action )
     *          It throws EkomUserMessageException when an error message should be sent to the front customer.
     *          It throws EkomInvalidArgumentException when the caller didn't provide the right arguments.
     *          Tip: use EcpServiceUtil::get method to get your arguments and handle this for you.
     *
     *
     *
     * Using this method helps implementing conventions explained in
     * class-modules/Ekom/doc/apis/ekom-service-api.md.
     *
     */
    public static function executeProcess(callable $process)
    {
        if (array_key_exists("action", $_GET)) {
            $action = $_GET['action'];
            $intent = (array_key_exists("intent", $_POST)) ? $_POST['intent'] : "";

            try {
                $out = call_user_func($process, $action, $intent);
            } catch (\Exception $e) {

                if ($e instanceof EkomInvalidArgumentException) {
                    $missing = $e->getMissingKey();
                    $out = [
                        '$$invalid$$' => "the $missing argument was not passed",
                    ];
                    // do you want to log those messages?
                    Hooks::call("Ekom_Ecp_logInvalidArgumentException", $e);
                } elseif ($e instanceof EkomUserMessageException) {
                    $out = [
                        '$$error$$' => $e->getMessage(),
                    ];
                } else {
                    $out = [
                        '$$error$$' => "An unexpected error occurred. It has been logged and we're working on it!",
                    ];
                    XLog::error("[Ekom module] - ecp - $e");
                }
            }
        } else {
            $out = [
                '$$invalid$$' => "the action identifier was not passed",
            ];
        }

        return $out;

    }


    public static function get($key, $throwEx = true, $default = null)
    {
        /**
         * ecp recommends that all params are passed via $_POST, except the action param.
         */
        $pool = $_POST;
        if (array_key_exists($key, $pool)) {
            $ret = $pool[$key];
            if ('true' === $ret) {
                $ret = true;
            }
            if ('false' === $ret) {
                $ret = false;
            }
            return $ret;
        }
        if (true === $throwEx) {
            throw EkomInvalidArgumentException::create()->setMissingKey($key);
        }
        return $default;
    }

}