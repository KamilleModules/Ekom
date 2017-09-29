<?php


namespace Module\Ekom\ListParams\ListBundleFactory;


use ListParams\Controller\PaginationFrame;
use ListParams\Controller\SortFrame;
use ListParams\ListBundle\LingListBundle;
use ListParams\ListParamsInterface;

class EkomListBundleFactoryHelper
{

    public static function getListBundleByItemsParams(array $items, ListParamsInterface $params)
    {
        $pagination = PaginationFrame::createByParams($params);
        $sortLabels = [];
        $fields = $params->getAllowedSortFields();
        foreach ($fields as $field) {
            $sortLabels[$field] = $field;
        }
        $sort = SortFrame::createByLabels($sortLabels, $params);
        return LingListBundle::createByItems($items, $params, $pagination, $sort);
    }

}