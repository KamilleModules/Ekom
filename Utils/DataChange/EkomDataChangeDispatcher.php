<?php

namespace Module\Ekom\Utils\DataChange;


use Dispatcher\Basic\BasicDispatcher;
use Module\Ekom\Cache\DerbyCache\EkomDerbyCache;
use Module\Ekom\Utils\E;

class EkomDataChangeDispatcher extends BasicDispatcher
{
    public function __construct()
    {
        parent::__construct();
        $this->on("dataChange", function ($identifier) {
            $p = explode('-', $identifier);
            $firstPart = array_shift($p);
            switch ($firstPart) {
                case "user.address":

                    // Ekom.UserAddressLayer.getUserAddresses.$userId.

                    $userId = array_shift($p);
                    EkomDerbyCache::create()->deleteByPrefix("Ekom.UserAddressLayer.getUserAddresses.$userId");

                    break;
                default:
                    break;
            }

        });
    }

}

