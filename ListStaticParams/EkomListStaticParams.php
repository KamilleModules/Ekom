<?php


namespace Module\Ekom\ListStaticParams;


use ListStaticParams\ListStaticParams;

class EkomListStaticParams extends ListStaticParams
{
    public function __construct()
    {
        parent::__construct();
        $this->setNbItemsPerPage(20);
    }


}