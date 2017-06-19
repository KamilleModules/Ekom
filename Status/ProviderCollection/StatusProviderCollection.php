<?php


namespace Module\Ekom\Status\ProviderCollection;


use Module\Ekom\Status\Provider\StatusProviderInterface;

class StatusProviderCollection implements StatusProviderCollectionInterface
{

    private $providers;

    public function __construct()
    {
        $this->providers = [];
    }

    public function all()
    {
        return $this->providers;
    }


    public function setProvider($name, StatusProviderInterface $provider)
    {
        $this->providers[$name] = $provider;
        return $this;
    }


}