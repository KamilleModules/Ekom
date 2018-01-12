<?php


namespace Module\Ekom\Back\Helper;

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
use QuickPdo\QuickPdo;

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
                    ->setLink(N::link("NullosAdmin_Ekom_Tags_List"))
                )
            )
            ->addItem(Item::create()
                ->setActive(true)
                ->setName("localization")
                ->setLabel("Localization")
                ->setIcon("fa fa-globe")
                ->setLink("#jo")
                ->addItem(Item::create()
                    ->setActive(true)
                    ->setName("currencies")
                    ->setLabel("Currencies")
                    ->setIcon("fa fa-money")
                    ->setLink(N::link("NullosAdmin_Ekom_Currency_List"))
                )
            )
        );
    }

    public static function NullosAdmin_User_hasRight(&$hasRight, $privilege)
    {
        // for now, everybody can do anything
        $hasRight = true;
    }

    public static function NullosAdmin_User_populateConnectedUser(array &$user)
    {


        $row = QuickPdo::fetch("
select * from ek_backoffice_user
where email=:email
",
            ['email' => $user['email']]
        );
        $shopId = 0;
        $langId = 0;
        $currencyId = 0;
        if (false !== $row) {
            $shopId = (int)$row['shop_id'];
            $langId = (int)$row['lang_id'];
            $currencyId = (int)$row['currency_id'];
        }

        $user['ekom'] = [
            'shop_id' => $shopId,
            'lang_id' => $langId,
            'currency_id' => $currencyId,
        ];
    }
}