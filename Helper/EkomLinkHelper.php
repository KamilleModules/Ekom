<?php


namespace Module\Ekom\Helper;

use Module\NullosAdmin\Helper\LinkHelper;

class EkomLinkHelper
{

    public static function getShopSectionLink($tab, array $params = [])
    {
        return LinkHelper::getSectionLink("NullosAdmin_Ekom_ShopMix", $tab, $params);
    }
}