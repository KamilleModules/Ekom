<?php


namespace Module\Ekom\Helper;

use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Ling\Z;
use Models\AdminSidebarMenu\Lee\LeeAdminSidebarMenuModel;
use Models\AdminSidebarMenu\Lee\Objects\Badge;
use Models\AdminSidebarMenu\Lee\Objects\Item;
use Models\AdminSidebarMenu\Lee\Objects\Section;
use Module\Ekom\Api\Layer\InvoiceLayer;
use Module\Ekom\Exception\EkomUserMessageException;
use Module\Ekom\Utils\Checkout\CurrentCheckoutData;
use Module\Ekom\Utils\E;
use Module\Ekom\Utils\Pdf\PdfHtmlInfoInterface;
use Module\NullosAdmin\Utils\N;

class BackHooksHelper
{


    public static function NullosAdmin_layout_sideBarMenuModelObject(LeeAdminSidebarMenuModel $sideBarMenuModel)
    {
        $sideBarMenuModel->addSection(Section::create()
            ->setName("ekom")
            ->setLabel("Ekom")
            ->setActive(true)
            ->addItem(Item::create()
                ->setActive(true)
                ->setName("catalog")
                ->setLabel("Catalog")
                ->setIcon("fa fa-book")
                ->setLink("#jo")
                ->addItem(Item::create()
                    ->setActive(true)
                    ->setName("tags")
                    ->setLabel("Tags")
                    ->setIcon("fa fa-tags")
                    ->setLink(N::link("NullosAdmin_Ekom_Catalog_Tags_List"))
                )
            )
        );
    }

    public static function NullosAdmin_User_hasRight(&$hasRight, $privilege)
    {
        // for now, everybody can do anything
        $hasRight = true;
    }
}