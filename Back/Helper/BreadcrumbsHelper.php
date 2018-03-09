<?php


namespace Module\Ekom\Back\Helper;

use Module\Ekom\Back\Config\EkomNullosConfig;
use Module\NullosAdmin\Utils\N;

class BreadcrumbsHelper
{
    public static function getBreadCrumbsModel(array $pages)
    {
        $ret = [];
        foreach ($pages as $id => $page) {
            if (is_string($page)) {

                $item = EkomNullosConfig::getBreadcrumbItem($page);
                if ($item['route']) {
                    $item['link'] = N::link($item['route']);
                } else {
                    $item['link'] = null;
                }
                $ret[$page] = $item;
            } else {
                /**
                 * To allow other module developers to use this helper if they want
                 */
                $item = $page;
                $ret[$id] = $item;
            }
        }
        return $ret;
    }
}