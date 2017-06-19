<?php


namespace Module\Ekom\Status\Provider;


class StatusProvider implements StatusProviderInterface
{

    protected $action2Codes;

    public function __construct()
    {
        $this->action2Codes = [];
    }

    /**
     * @param $ekomAction
     * @return string|false,
     *          the status code corresponding to the ekom action that has just been executed,
     *          or false if there is no correspondence for this action.
     */
    public function getCode($ekomAction)
    {
        if (array_key_exists($ekomAction, $this->action2Codes)) {
            return $this->action2Codes[$ekomAction];
        }
        return false;
    }

    public function setAction2Codes(array $action2Codes)
    {
        $this->action2Codes = $action2Codes;
        return $this;
    }


}