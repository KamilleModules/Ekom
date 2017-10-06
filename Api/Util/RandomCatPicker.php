<?php


namespace Module\Ekom\Api\Util;


use Module\Ekom\Api\EkomApi;
use QuickPdo\QuickPdo;

class RandomCatPicker
{


    private $rootCatName;
    private $catIds;


    public function __construct($rootCatName = 'home')
    {
        $this->rootCatName = $rootCatName;
        $catId = QuickPdo::fetch("select id from ek_category where name='$rootCatName'", [], \PDO::FETCH_COLUMN);
        $this->catIds = EkomApi::inst()->categoryLayer()->getDescendantCategoryIdTree($catId);
    }


    public static function create($rootCatName = 'home')
    {
        return new static($rootCatName);
    }


    public function getCat()
    {
        $nCat = count($this->catIds) - 1;
        return $this->catIds[rand(0, $nCat)];
    }

}