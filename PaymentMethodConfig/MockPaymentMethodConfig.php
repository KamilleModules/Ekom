<?php


namespace Module\Ekom\PaymentMethodConfig;


class MockPaymentMethodConfig implements PaymentMethodConfigInterface
{

    private $config;


    public static function create()
    {
        return new static();
    }

    public function setConfig($config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getConfig()
    {
        if (is_array($this->config)) {
            return $this->config;
        } elseif (is_callable($this->config)) {
            return call_user_func($this->config);
        }
        // error?
        return [];
    }


}
