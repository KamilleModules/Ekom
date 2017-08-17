<?php


namespace Module\Ekom\Utils;


use Module\Ekom\Api\EkomApi;

class ReferenceProvider
{

    /**
     * a callable that creates a unique reference
     */
    private $callback;


    public function getNewReference()
    {

        if (null !== $this->callback) {
            return call_user_func($this->callback);
        }
        return date('Ymd-His') . '-' . sprintf('%04s', (EkomApi::inst()->orderLayer()->countOrders() + 1));
    }

    public function setCallback(callable $callback)
    {
        $this->callback = $callback;
        return $this;
    }


}