<?php


namespace Module\Ekom\Status\ProviderCollection;


use Module\Ekom\Status\Provider\StatusProviderInterface;

interface StatusProviderCollectionInterface
{

    /**
     * @return array of name => StatusProviderInterface
     */
    public function all();

    public function setProvider($name, StatusProviderInterface $provider);

}