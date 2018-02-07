<?php


namespace Module\Ekom\Back\Helper;

use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Ling\Z;
use Models\AdminSidebarMenu\Lee\LeeAdminSidebarMenuModel;
use Models\AdminSidebarMenu\Lee\Objects\Badge;
use Models\AdminSidebarMenu\Lee\Objects\Item;
use Models\AdminSidebarMenu\Lee\Objects\Section;
use Module\Ekom\Api\Layer\CurrencyLayer;
use Module\Ekom\Api\Layer\InvoiceLayer;
use Module\Ekom\Api\Layer\LangLayer;
use Module\Ekom\Api\Layer\ShopLayer;
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
                ->setLink("#")
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
                ->setName("shop")
                ->setLabel("Shop")
                ->setIcon("fa fa-building-o")
                ->setLink(N::link("NullosAdmin_Ekom_ShopMix"))
//                ->addItem(Item::create()
//                    ->setActive(true)
//                    ->setName("currency")
//                    ->setLabel("Currencies")
//                    ->setIcon("fa fa-money")
//                    ->setLink(N::link("NullosAdmin_Ekom_ShopCurrency_List"))
//                )
//                ->addItem(Item::create()
//                    ->setActive(true)
//                    ->setName("lang")
//                    ->setLabel("Lang")
//                    ->setIcon("fa fa-language")
//                    ->setLink(N::link("NullosAdmin_Ekom_Lang_List"))
//                )
            )
            ->addItem(Item::create()
                ->setActive(true)
                ->setName("customer_container")
                ->setLabel("Client")
                ->setIcon("fa fa-user")
                ->setLink("#")
                ->addItem(Item::create()
                    ->setActive(true)
                    ->setName("user")
                    ->setLabel("Client")
                    ->setIcon("fa fa-user")
                    ->setLink(N::link("NullosAdmin_Ekom_User_List"))
                )
                ->addItem(Item::create()
                    ->setActive(true)
                    ->setName("user-address")
                    ->setLabel("Adresses")
                    ->setIcon("fa fa-home")
                    ->setLink(N::link("NullosAdmin_Ekom_UserAddress_List"))
                )
                ->addItem(Item::create()
                    ->setActive(true)
                    ->setName("user_group")
                    ->setLabel("Groupe d'utilisateur")
                    ->setIcon("fa fa-group")
                    ->setLink(N::link("NullosAdmin_Ekom_UserGroup_List"))
                )
            )
            ->addItem(Item::create()
                ->setActive(true)
                ->setName("super_admin")
                ->setLabel("Super Admin")
                ->setIcon("fa fa-shield")
                ->setLink("#")
                ->addItem(Item::create()
                    ->setActive(true)
                    ->setName("currency")
                    ->setLabel("Currency")
                    ->setIcon("fa fa-money")
                    ->setLink(N::link("NullosAdmin_Ekom_Currency_List"))
                )
                ->addItem(Item::create()
                    ->setActive(true)
                    ->setName("lang")
                    ->setLabel("Lang")
                    ->setIcon("fa fa-language")
                    ->setLink(N::link("NullosAdmin_Ekom_Lang_List"))
                )
                ->addItem(Item::create()
                    ->setActive(true)
                    ->setName("payment_method")
                    ->setLabel("Payment method")
                    ->setIcon("fa fa-bank")
                    ->setLink(N::link("NullosAdmin_Ekom_PaymentMethod_List"))
                )
                ->addItem(Item::create()
                    ->setActive(true)
                    ->setName("carrier")
                    ->setLabel("Carrier")
                    ->setIcon("fa fa-truck")
                    ->setLink(N::link("NullosAdmin_Ekom_Carrier_List"))
                )
                ->addItem(Item::create()
                    ->setActive(true)
                    ->setName("country")
                    ->setLabel("Country")
                    ->setIcon("fa fa-globe")
                    ->setLink("#")
                    ->addItem(Item::create()
                        ->setActive(true)
                        ->setName("country_code")
                        ->setLabel("Country")
                        ->setLink(N::link("NullosAdmin_Ekom_Country_List"))
                    )
                    ->addItem(Item::create()
                        ->setActive(true)
                        ->setName("country_lang")
                        ->setLabel("Country translation")
                        ->setLink(N::link("NullosAdmin_Ekom_CountryTranslation_List"))
                    )
                )
                ->addItem(Item::create()
                    ->setActive(true)
                    ->setName("address")
                    ->setLabel("Address")
                    ->setIcon("fa fa-home")
                    ->setLink(N::link("NullosAdmin_Ekom_Address_List"))
                )
                ->addItem(Item::create()
                    ->setActive(true)
                    ->setName("tax_container")
                    ->setLabel("Tax")
                    ->setIcon("fa fa-calculator")
                    ->setLink(N::link("NullosAdmin_Ekom_Tax_List"))
                )
                ->addItem(Item::create()
                    ->setActive(true)
                    ->setName("product_attribute_container")
                    ->setLabel("Product attribute")
                    ->setIcon("fa fa-share-alt-square")
                    ->setLink("#")
                    ->addItem(Item::create()
                        ->setActive(true)
                        ->setName("product_attribute")
                        ->setLabel("Product attribute")
                        ->setLink(N::link("NullosAdmin_Ekom_ProductAttribute_List"))
                    )
                    ->addItem(Item::create()
                        ->setActive(true)
                        ->setName("product_attribute_value")
                        ->setLabel("Product attribute value")
                        ->setLink(N::link("NullosAdmin_Ekom_ProductAttributeValue_List"))
                    )
                )
            )
            ->addItem(Item::create()
                ->setActive(true)
                ->setName("fabric")
                ->setLabel("Fabrique des produits")
                ->setIcon("fa fa-cubes")
                ->setLink("#")
                ->addItem(Item::create()
                    ->setActive(true)
                    ->setName("product_card")
                    ->setLabel("Cartes")
                    ->setIcon("fa fa-circle-o")
                    ->setLink(N::link("NullosAdmin_Ekom_ProductCard_List"))
                )
                ->addItem(Item::create()
                    ->setActive(true)
                    ->setName("product")
                    ->setLabel("Produits")
                    ->setIcon("fa fa-dot-circle-o")
                    ->setLink(N::link("NullosAdmin_Ekom_Product_List"))
                )
            )
            ->addItem(Item::create()
                ->setActive(true)
                ->setName("tools")
                ->setLabel("Tools")
                ->setIcon("fa fa-briefcase")
                ->setLink("#")
                ->addItem(Item::create()
                    ->setActive(true)
                    ->setName("app_diagnostic")
                    ->setLabel("Application sanity check")
                    ->setIcon("fa fa-medkit")
                    ->setLink(N::link("NullosAdmin_Ekom_Tools_AppSanityCheck"))
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
        $shopHost = "";
        $langId = 0;
        $langIsoCode = "";
        $currencyId = 0;
        $currencyIsoCode = "";
        if (false !== $row) {
            $shopId = (int)$row['shop_id'];
            $langId = (int)$row['lang_id'];
            $currencyId = (int)$row['currency_id'];
            $shopHost = ShopLayer::getHostById($shopId);
            $langIsoCode = LangLayer::getIsoCodeById($langId);
            $currencyIsoCode = CurrencyLayer::getIsoCodeById($currencyId);
        }

        $user['ekom'] = [
            'shop_id' => $shopId,
            'shop_host' => $shopHost,
            'lang_id' => $langId,
            'lang_iso_code' => $langIsoCode,
            'currency_id' => $currencyId,
            'currency_iso_code' => $currencyIsoCode,
        ];
    }
}