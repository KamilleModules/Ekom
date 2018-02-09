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
use Module\Ekom\Api\Layer\ProductLayer;
use Module\Ekom\Api\Layer\ShopLayer;
use Module\Ekom\Back\User\EkomNullosUser;
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
                ->setName("marketing")
                /**
                 * Will contain:
                 * - discounts
                 * - coupons
                 * - bundle
                 * - groups (although this one could be in management)
                 */
                ->setLabel("Marketing")
                ->setIcon("fa fa-crosshairs")
                ->setLink("#")
                ->addItem(Item::create()
                    ->setActive(true)
                    ->setName("product_group")
                    ->setLabel("Groupes de produits")
                    ->setIcon("fa fa-th-large")
                    ->setLink(N::link("NullosAdmin_Ekom_ProductGroup_List"))
                )
            )
            ->addItem(Item::create()
                ->setActive(true)
                ->setName("catalog")
                ->setLabel("Catalogue")
                ->setIcon("fa fa-book")
                ->setLink("#")
                ->addItem(Item::create()
                    ->setActive(true)
                    ->setName("catalog_products_container")
                    ->setLabel("Products")
                    ->setIcon("fa fa-dot-circle-o")
                    ->setLink(N::link("NullosAdmin_Ekom_ProductGroup_List"))
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
                ->addItem(Item::create()
                    ->setActive(true)
                    ->setName("product_feature_container")
                    ->setLabel("Product feature")
                    ->setIcon("fa fa-cogs")
                    ->setLink("#")
                    ->addItem(Item::create()
                        ->setActive(true)
                        ->setName("feature")
                        ->setLabel("Product feature")
                        ->setLink(N::link("NullosAdmin_Ekom_Feature_List"))
                    )
                    ->addItem(Item::create()
                        ->setActive(true)
                        ->setName("feature_value")
                        ->setLabel("Product feature value")
                        ->setLink(N::link("NullosAdmin_Ekom_FeatureValue_List"))
                    )
                )
                ->addItem(Item::create()
                    ->setActive(true)
                    ->setName("product_comment")
                    ->setLabel("Product comment")
                    ->setIcon("fa fa-comments-o")
                    ->setLink(N::link("NullosAdmin_Ekom_ProductComment_List"))
                )
                ->addItem(Item::create()
                    ->setActive(true)
                    ->setName("tags")
                    ->setLabel("Tags")
                    ->setIcon("fa fa-tags")
                    ->setLink(N::link("NullosAdmin_Ekom_Tag_List"))
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


    public static function NullosAdmin_SokoForm_NullosBootstrapRenderer_AutocompleteInitialValue(&$label, $action, $value)
    {
        if ($value) {

            $langId = EkomNullosUser::getEkomValue("lang_id");

            switch ($action) {
                case "auto.address":
                    $value = (int)$value;
                    $label = QuickPdo::fetch("
                    select 
concat(
  a.id, 
  '. ',
  a.first_name, 
  ' ',
  a.last_name, 
  ' ',
  a.address, 
  ' ',
  a.postcode, 
  ' ',
  a.city, 
  ' ',
  UPPER(l.label)
  ) as label
from ek_address a 
inner join ek_country c on c.id=a.country_id
inner join ek_country_lang l on l.country_id=c.id
where a.id=$value
and l.lang_id=$langId
             
                    ", [], \PDO::FETCH_COLUMN);
                    break;
                case "auto.category":
                    $value = (int)$value;
                    $label = QuickPdo::fetch("
select 
concat(
  id, 
  '. ',
  `name`
  ) as label
from ek_category  
where 
id=$value
", [], \PDO::FETCH_COLUMN);
                    break;
                case "auto.discount":
                    $value = (int)$value;
                    $label = QuickPdo::fetch("
select 
l.label
from ek_discount d 
inner join ek_discount_lang l on l.discount_id=d.id
where 
d.id=$value
and l.lang_id=$langId
", [], \PDO::FETCH_COLUMN);
                    break;
                case "auto.product":
                    $value = (int)$value;
                    $label = QuickPdo::fetch("
select
concat( 
case when pl.label is not null and pl.label != '' 
then
concat (pl.label, '. ')
else 
''
end, 
concat ('ref=', p.reference)
) as label

from ek_product p
left join ek_product_lang pl on pl.product_id=p.id

 
where 
p.id=$value
", [], \PDO::FETCH_COLUMN);
                    break;
                case "auto.product_card":
                    $value = (int)$value;
                    $label = QuickPdo::fetch("
select 
concat (product_card_id, '. ', label) as label
from ek_product_card_lang  
where 
product_card_id=$value
and lang_id=$langId
", [], \PDO::FETCH_COLUMN);
                    break;
                case "auto.tag":
                    $value = (int)$value;
                    $label = QuickPdo::fetch("
select 
concat (t.id, '. ', t.name) as label
from ek_tag t  
where t.id=$value
", [], \PDO::FETCH_COLUMN);
                    break;
                case "auto.user":
                    $value = (int)$value;
                    $label = QuickPdo::fetch("
select 
concat (
CASE WHEN first_name != '' OR last_name != ''
THEN
concat(first_name, ' ', last_name, ':')
ELSE
''
END,
email,
CASE WHEN pseudo != ''
THEN
concat(' (', pseudo, ')')
ELSE
''
END 



) as label
from ek_user  
where id=$value
", [], \PDO::FETCH_COLUMN);
                    break;
                default:
                    break;
            }
        }
    }
}