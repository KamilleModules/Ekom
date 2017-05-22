<?php


namespace Module\Ekom\Api;

use Kamille\Services\XLog;


/**
 * Singleton
 */
class EkomApi extends GeneratedEkomApi
{
    //--------------------------------------------
    //
    //--------------------------------------------
    protected function log($type, $message) // override me
    {
        XLog::log($type, $message);
    }
}