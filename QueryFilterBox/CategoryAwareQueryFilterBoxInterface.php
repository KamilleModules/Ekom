<?php


namespace Module\Ekom\QueryFilterBox;


interface CategoryAwareQueryFilterBoxInterface
{
    public function getCategoryId();

    public function setCategoryId($categoryId);
}