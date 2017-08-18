<?php


namespace Module\Ekom\Utils;


use Module\Ekom\Api\EkomApi;

class ReferenceProvider
{

    /**
     * a callable that creates a unique reference
     */
    private $callbacks;


    public function getNewReference($type = null)
    {
        if (array_key_exists($type, $this->callbacks)) {
            return call_user_func($this->callbacks[$type]);
        }
        return date('Ymd-His') . '-' . sprintf('%04s', (EkomApi::inst()->orderLayer()->countOrders() + 1));
    }

    public function setCallback($type, callable $callback)
    {
        $this->callbacks[$type] = $callback;
        return $this;
    }


}