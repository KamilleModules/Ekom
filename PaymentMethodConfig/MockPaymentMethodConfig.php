<?php


namespace Module\Ekom\PaymentMethodConfig;


class MockPaymentMethodConfig implements PaymentMethodConfigInterface
{

    private $config;
    private $defaultOptions;


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


}
