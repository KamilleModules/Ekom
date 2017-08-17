<?php


namespace Module\Ekom\PaymentMethodHandler;


class MockPaymentMethodHandler implements PaymentMethodHandlerInterface
{

    private $config;
    private $defaultOptions;
    private $payCallback;


    public function __construct()
    {
        $this->defaultOptions = [];
    }


    public static function create()
    {
        return new static();
    }

    public function setConfig($config)
    {
        $this->config = $config;
        return $this;
    }

    public function setPayCallback(callable $payCallback)
    {
        $this->payCallback = $payCallback;
        return $this;
    }


    /**
     * @param callable|array $defaultOptions ,
     *              the callable must return an array
     * @return $this
     */
    public function setDefaultOptions($defaultOptions)
    {
        $this->defaultOptions = $defaultOptions;
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

    public function getDefaultOptions($configuration = null)
    {
        $ret = $this->defaultOptions;
        if (is_callable($ret)) {
            $ret = call_user_func($ret, $configuration);
        }
        if (is_array($configuration)) {
            $ret = array_merge($ret, $configuration);
        }
        return $ret;
    }

    public function pay(array $extendedOrderModel)
    {
        if (is_callable($this->payCallback)) {
            return call_user_func($this->payCallback, $extendedOrderModel);
        }
        return [];
    }


}
