<?php


namespace Module\Ekom\QueryFilterBox\QueryFilterBox;


use Module\Ekom\Api\EkomApi;
use Module\Ekom\QueryFilterBox\CategoryAwareQueryFilterBoxInterface;
use QueryFilterBox\Query\Query;
use QueryFilterBox\QueryFilterBox\QueryFilterBox;


class CategoryQueryFilterBox extends QueryFilterBox implements CategoryAwareQueryFilterBoxInterface
{

    private $categoryId;


    public function __construct()
    {
        parent::__construct();
        $this->categoryId = null;
    }

    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;
        return $this;
    }

    public function getCategoryId()
    {
        return $this->categoryId;
    }


    public static function create()
    {
        return new static();
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    protected function doDecorateQuery(Query $query, array $pool, array &$usedPool)
    {
        if (array_key_exists("category", $pool)) {
            $usedPool[] = "category";
            $categoryId = $pool['category'];
            $catIds = EkomApi::inst()->categoryLayer()->getDescendantCategoryIdTree($categoryId);

            $query->addWhere("
chc.category_id in(" . implode(', ', $catIds) . ")
        ");

        }
    }
}