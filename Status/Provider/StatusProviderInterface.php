<?php


namespace Module\Ekom\Status\Provider;


interface StatusProviderInterface
{

    /**
     * @param $ekomAction
     * @return string|false,
     *          the status code corresponding to the ekom action that has just been executed,
     *          or false if there is no correspondence for this action.
     */
    public function getCode($ekomAction);
}