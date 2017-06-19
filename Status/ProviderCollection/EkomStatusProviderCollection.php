<?php


namespace Module\Ekom\Status\ProviderCollection;


use Module\Ekom\Status\Provider\LeeStatusProvider;

class EkomStatusProviderCollection extends StatusProviderCollection
{
    public function __construct()
    {
        parent::__construct();
        $this->setProvider("lee", new LeeStatusProvider());
    }


}