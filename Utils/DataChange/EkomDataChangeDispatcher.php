<?php

namespace Module\Ekom\Utils\DataChange;


use Dispatcher\Basic\BasicDispatcher;

class EkomDataChangeDispatcher extends BasicDispatcher
{
    public function __construct()
    {
        parent::__construct();
        $this->on("dataChange", function ($identifier) {
            switch ($identifier) {
                case "":
                    break;
                default:
                    break;
            }
        });
    }

}

