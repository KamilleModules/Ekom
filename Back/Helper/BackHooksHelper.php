<?php


namespace Module\Ekom\Back\Helper;

use Controller\NullosAdmin\Back\NullosStandardPageController;
use Core\Services\A;
use Core\Services\Hooks;
use Kamille\Utils\Claws\ClawsInterface;
use Kamille\Utils\Claws\ClawsWidget;
use Kamille\Utils\Morphic\Generator2\Helper\LingFrenchMorphicGeneratorHelper;
use Models\AdminSidebarMenu\Lee\LeeAdminSidebarMenuModel;
use Models\AdminSidebarMenu\Lee\Objects\Item;
use Models\AdminSidebarMenu\Lee\Objects\Section;
use Module\Ekom\Back\User\EkomNullosUser;
use Module\Ekom\Back\WidgetModel\Dashboard\DefaultDashboardModel;
use Module\Ekom\Exception\EkomException;
use Module\Ekom\Helper\Stats\Modules\BestCategoriesControllerModule;
use Module\Ekom\Helper\Stats\Modules\BestCouponsControllerModule;
use Module\Ekom\Helper\Stats\Modules\BestCustomersControllerModule;
use Module\Ekom\Helper\Stats\Modules\BestProvidersControllerModule;
use Module\Ekom\Helper\Stats\Modules\BestSellersControllerModule;
use Module\Ekom\Helper\Stats\Modules\BestSellsControllerModule;
use Module\Ekom\Helper\Stats\Modules\CustomerAccountsControllerModule;
use Module\Ekom\Helper\Stats\Modules\CustomerInfoControllerModule;
use Module\Ekom\Helper\Stats\Modules\OrdersAndProfitControllerModule;
use Module\Ekom\Helper\Stats\Modules\ProductDetailsControllerModule;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;

class BackHooksHelper
{


    public static function Ekom_Stats_Dashboard_getModuleHandler(&$moduleHandler, $currentModule)
    {
        switch ($currentModule) {
            case "orders_and_profit":
                $moduleHandler = OrdersAndProfitControllerModule::getModuleHandler();
                break;
            case "customer_accounts":
                $moduleHandler = CustomerAccountsControllerModule::getModuleHandler();
                break;
            case "product_details":
                $moduleHandler = ProductDetailsControllerModule::getModuleHandler();
                break;
            case "customer_info":
                $moduleHandler = CustomerInfoControllerModule::getModuleHandler();
                break;
            case "best_categories":
                $moduleHandler = BestCategoriesControllerModule::getModuleHandler();
                break;
            case "best_sells":
                $moduleHandler = BestSellsControllerModule::getModuleHandler();
                break;
            case "best_customers":
                $moduleHandler = BestCustomersControllerModule::getModuleHandler();
                break;
            case "best_coupons":
                $moduleHandler = BestCouponsControllerModule::getModuleHandler();
                break;
            case "best_providers":
                $moduleHandler = BestProvidersControllerModule::getModuleHandler();
                break;
            case "best_sellers":
                $moduleHandler = BestSellersControllerModule::getModuleHandler();
                break;
            default:
                break;
        }
    }


    public static function NullosAdmin_SokoFormRenderer_getRenderIdentifier(&$identifier, $className)
    {
        $ret = null;
        switch ($className) {
            case "SokoCouponRulesFreeHtmlControl":
                $identifier = "free";
                break;
        }
    }


    public static function NullosAdmin_MorphicHelper_StandardColTransformer(callable &$func = null, $name, array $options = [])
    {
        switch ($name) {
            case "Ekom.price":
                $func = function ($value, array $row) use ($options) {
                    if ($value) {
                        return '<div class="badge" style="background:#3577da">' . E::price($value) . '</div>';
                    }
                };
                break;
            case "Ekom.saving":
                $func = function ($value, array $row) use ($options) {
                    if ($value) {
                        return '<div class="badge" style="background:#0fa805">' . E::price($value) . '</div>';
                    }
                };
                break;
            default:
                break;
        }
    }

    public static function ApplicationMorphicGenerator_getMorphicGeneratorTranslationFiles(array &$info)
    {
        LingFrenchMorphicGeneratorHelper::injectTranslations($info, "ek_", "Ekom");
    }


    public static function NullosAdmin_Back_getElementAvatar(&$avatar, $table, array $context = [])
    {

    }


    public static function NullosAdmin_layout_sideBarMenuModelObject(LeeAdminSidebarMenuModel $sideBarMenuModel)
    {


        $section = Section::create()
            ->setName("ekom")
            ->setLabel("Ekom")
            ->setActive(true);
        $sideBarMenuModel->addSection($section);


        $productGroupsItem = Item::create()
            ->setActive(true)
            ->setName("catalog_product_groups")
            ->setLabel("Groupes de produits")
            ->setLink("#")
            ->addItem(Item::create()
//                        ->setActive(true)
                ->setName("catalog_product_groups_default")
                ->setLabel("Groupes basiques")
                ->setLink(A::link("Ekom_Catalog_ProductGroup_List"))
            )
            ->addItem(Item::create()
//                        ->setActive(true)
                ->setName("catalog_product_groups_related")
                ->setLabel("Groupes liés à une carte")
                ->setLink(A::link("Ekom_Catalog_RelatedCardProductGroup_List"))
            );

        $catalogItem = Item::create()
            ->setActive(true)
            ->setName("catalog")
            ->setLabel("Catalogue")
            ->setIcon("fa fa-book")
            ->setLink("#")
            ->addItem(Item::create()
                ->setActive(true)
                ->setName("catalog_products")
                ->setLabel("Cartes et produits")
//                    ->setIcon("fa fa-dot-circle-o")
                ->setLink(A::link("Ekom_Catalog_Product_List"))
            )
            ->addItem(Item::create()
                ->setActive(true)
                ->setName("catalog_categories")
                ->setLabel("Catégories")
//                    ->setIcon("fa fa-dot-circle-o")
                ->setLink("#")
                ->addItem(Item::create()
                    ->setActive(true)
                    ->setName("catalog_categories_list")
                    ->setLabel("Liste des catégories")
//                    ->setIcon("fa fa-dot-circle-o")
                    ->setLink(A::link("Ekom_Catalog_Category_List"))
                )
                ->addItem(Item::create()
                    ->setActive(true)
                    ->setName("catalog_categories_tree")
                    ->setLabel("Arbre des catégories")
//                    ->setIcon("fa fa-dot-circle-o")
                    ->setLink(A::link("Ekom_Catalog_CategoryTree_List"))
                )
            )
            ->addItem(Item::create()
                ->setActive(true)
                ->setName("catalog_bundles")
                ->setLabel("Packs")
                ->setLink(A::link("Ekom_Catalog_Bundle_List"))
            )
            ->addItem($productGroupsItem)
            ->addItem(Item::create()
                ->setActive(true)
                ->setName("catalog_attributes")
                ->setLabel("Attributs")
                ->setLink(A::link("Ekom_Catalog_ProductAttribute_List"))
            )
            ->addItem(Item::create()
                ->setActive(true)
                ->setName("catalog_manufacturers")
                ->setLabel("Fabricants")
                ->setLink(A::link("Ekom_Catalog_Manufacturer_List"))
            )
            ->addItem(Item::create()
                ->setActive(true)
                ->setName("catalog_providers")
                ->setLabel("Fournisseurs")
                ->setLink(A::link("Ekom_Catalog_Provider_List"))
            )
            ->addItem(Item::create()
                ->setActive(true)
                ->setName("catalog_tags")
                ->setLabel("Tags")
                ->setLink(A::link("Ekom_Catalog_Tag_List"))
            )
            ->addItem(Item::create()
                ->setActive(true)
                ->setName("catalog_comments")
                ->setLabel("Commentaires produits")
                ->setLink(A::link("Ekom_Catalog_ProductCommentList"))
            );


        $section
            //--------------------------------------------
            // CATALOG
            //--------------------------------------------
            ->addItem($catalogItem)
            //--------------------------------------------
            // ORDERS
            //--------------------------------------------
            ->addItem(Item::create()
                ->setActive(true)
                ->setName("orders")
                ->setLabel("Commandes")
                ->setIcon("fa fa-credit-card")
                ->setLink("#")
                ->addItem(Item::create()
                    ->setActive(true)
                    ->setName("orders_orders")
                    ->setLabel("Commandes")
//                    ->setIcon("fa fa-dot-circle-o")
                    ->setLink(A::link("Ekom_Orders_Order_List"))
                )
                ->addItem(Item::create()
                    ->setActive(true)
                    ->setName("orders_order_statuses")
                    ->setLabel("Statuts de commande")
//                    ->setIcon("fa fa-dot-circle-o")
                    ->setLink(A::link("Ekom_Orders_OrderStatus_List"))
                )
            );


        $configItem = Item::create()
            ->setActive(true)
            ->setName("config")
            ->setLabel("Configuration")
            ->setIcon("fa fa-cogs")
            ->setLink("#")
            ->addItem(Item::create()
                ->setActive(true)
                ->setName("config_config")
                ->setLabel("Configuration")
//                    ->setIcon("fa fa-spinner")
                ->setLink(A::link("Ekom_Config_Config_List"))
            );


        $utilsItem = Item::create()
            ->setActive(true)
            ->setName("utils")
            ->setLabel("Utilitaires")
            ->setIcon("fa fa-wrench")
            ->setLink("#")
            ->addItem(Item::create()
                ->setActive(true)
                ->setName("cache-util")
                ->setLabel("Cache")
//                    ->setIcon("fa fa-spinner")
                ->setLink(A::link("Ekom_Utils_CacheManager"))
            );


        $usersItem = Item::create()
            ->setActive(true)
            ->setName("users")
            ->setLabel("Clients")
            ->setIcon("fa fa-child")
            ->setLink("#")
            ->addItem(Item::create()
                ->setActive(true)
                ->setName("users_user")
                ->setLabel("Clients")
//                    ->setIcon("fa fa-spinner")
                ->setLink(A::link("Ekom_Users_User_List"))
            )
            ->addItem(Item::create()
                ->setActive(true)
                ->setName("users_address")
                ->setLabel("Adresses")
//                    ->setIcon("fa fa-spinner")
                ->setLink(A::link("Ekom_Users_Address_List"))
            )
            ->addItem(Item::create()
                ->setActive(true)
                ->setName("users_group")
                ->setLabel("Groupes")
//                    ->setIcon("fa fa-spinner")
                ->setLink(A::link("Ekom_Users_Group_List"))
            )
            ->addItem(Item::create()
                ->setActive(true)
                ->setName("users_gender")
                ->setLabel("Titres de civilité")
//                    ->setIcon("fa fa-spinner")
                ->setLink(A::link("Ekom_Users_Gender_List"))
            );


        $discountItem = Item::create()
            ->setActive(true)
            ->setName("discounts")
            ->setLabel("Promotions")
            ->setIcon("fa fa-tag")
            ->setLink("#")
            ->addItem(Item::create()
                ->setActive(true)
                ->setName("discounts_discount")
                ->setLabel("Réductions produit")
//                    ->setIcon("fa fa-spinner")
                ->setLink(A::link("Ekom_Discounts_Discount_List"))
            )
            ->addItem(Item::create()
                ->setActive(true)
                ->setName("discounts_coupon")
                ->setLabel("Coupons")
//                    ->setIcon("fa fa-spinner")
                ->setLink(A::link("Ekom_Discounts_Coupon_List"))
            );

        $carrierItem = Item::create()
            ->setActive(true)
            ->setName("carriers")
            ->setLabel("Transporteurs")
            ->setIcon("fa fa-truck")
            ->setLink("#")
            ->addItem(Item::create()
                ->setActive(true)
                ->setName("carriers_carrier")
                ->setLabel("Transporteurs")
//                    ->setIcon("fa fa-spinner")
                ->setLink(A::link("Ekom_Carriers_Carrier_List"))
            );


        $localizationItem = Item::create()
            ->setActive(true)
            ->setName("localization")
            ->setLabel("Localisation")
            ->setIcon("fa fa-globe")
            ->setLink("#")
            ->addItem(Item::create()
                ->setActive(true)
                ->setName("localization_country")
                ->setLabel("Pays")
//                    ->setIcon("fa fa-spinner")
                ->setLink(A::link("Ekom_Localization_Country_List"))
            )
            ->addItem(Item::create()
                ->setActive(true)
                ->setName("localization_taxrule")
                ->setLabel("Règles de taxe")
//                    ->setIcon("fa fa-spinner")
                ->setLink(A::link("Ekom_Localization_TaxRule_List"))
            )
            ->addItem(Item::create()
                ->setActive(true)
                ->setName("localization_tax")
                ->setLabel("Taxes")
//                    ->setIcon("fa fa-spinner")
                ->setLink(A::link("Ekom_Localization_Tax_List"))
            );


        $seoItem = Item::create()
            ->setActive(true)
            ->setName("seo")
            ->setLabel("Seo")
            ->setIcon("fa fa-bullhorn")
            ->setLink("#")
            ->addItem(Item::create()
                ->setActive(true)
                ->setName("seo_page")
                ->setLabel("Pages")
//                    ->setIcon("fa fa-spinner")
                ->setLink(A::link("Ekom_Seo_Page_List"))
            );


        $statItem = Item::create()
            ->setActive(true)
            ->setName("stats")
            ->setLabel("Statistiques")
            ->setIcon("fa fa-bar-chart")
            ->setLink("#")
            ->addItem(Item::create()
                ->setActive(true)
                ->setName("stats_statistics")
                ->setLabel("Statistiques")
//                    ->setIcon("fa fa-spinner")
                ->setLink(A::link("Ekom_Stats_Statistics_Dashboard"))
            );


        $section
            ->addItem($configItem)
//            ->addItem($utilsItem)
            ->addItem($usersItem)
            ->addItem($discountItem)
            ->addItem($carrierItem)
            ->addItem($localizationItem)
            ->addItem($seoItem)
            ->addItem($statItem);


        $menuItems = [
            'Ekom.section' => $section,
            'Ekom.config' => $configItem,
            'Ekom.utils' => $utilsItem,
            'Ekom.users' => $usersItem,
            'Ekom.carriers' => $carrierItem,
            'Ekom.localization' => $localizationItem,
            'Ekom.seo' => $seoItem,
            'Ekom.stats' => $statItem,
            'Ekom.catalog' => $catalogItem,
            'Ekom.catalog.productGroups' => $productGroupsItem,
        ];

        Hooks::call("Ekom_decorateLeftMenu", $menuItems);

    }

    public static function NullosAdmin_prepareClaws(ClawsInterface $claws, $type = null, NullosStandardPageController $controller)
    {


        //--------------------------------------------
        // HOME SPECIFIC
        //--------------------------------------------
        if ('home' === $type) {

            $claws->removeWidget("maincontent.body");
            $model = DefaultDashboardModel::getModel();


            $pageTop = $controller->pageTop();
            $pageTop->breadcrumbs()->addLink("Tableau de bord");
            $pageTop->setTitle("Tableau de bord");


            $claws
                //--------------------------------------------
                // MAIN
                //--------------------------------------------
                ->setWidget("maincontent.body", ClawsWidget::create()
                    ->setTemplate('NullosAdmin/Main/Dashboard/default')
                    ->setConf($model)
                );
        }

    }

    public static function NullosAdmin_User_hasRight(&$hasRight, $privilege)
    {
        // for now, everybody can do anything
        $hasRight = true;
    }


    public static function NullosAdmin_SokoForm_NullosBootstrapRenderer_AutocompleteInitialValueMultiple(array &$value2Labels, $action, array $values)
    {
        if ($values) {

            $query = "";
            switch ($action) {
                case "auto.address":
                    $sValues = "'" . implode("', '", $values) . "'";
                    $query = "
select
a.id,                     
concat(
  a.id, 
  '. ',
  a.libelle, 
  ' ',
  a.address, 
  ' ',
  a.postcode, 
  ' ',
  a.city, 
  ' ',
  UPPER(c.label)
  ) as label
from ek_address a 
inner join ek_country c on c.id=a.country_id
where a.id in ($sValues)
             
                    ";
                    break;
                case "auto.product":
                    $sValues = "'" . implode("', '", $values) . "'";
                    $query = "
select
p.id,
concat( 
  label, 
  concat (' ref=', reference)
) as label

from ek_product p 
inner join ek_product_reference pr on pr.product_id=p.id

where 
p.id in ($sValues)
             
                    ";

                    break;
                case "auto.product_card":
                    $sValues = "'" . implode("', '", $values) . "'";
                    $query = "
select 
id,
concat (id, '. ', label) as label
from ek_product_card  
where 
id in ($sValues)";
                    break;
                default:
                    throw new EkomException("Unknown action $action");
                    break;

            }

            $value2Labels = QuickPdo::fetchAll($query, [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
        }
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
  a.libelle, 
  ' ',
  a.address, 
  ' ',
  a.postcode, 
  ' ',
  a.city, 
  ' ',
  UPPER(c.label)
  ) as label
from ek_address a 
inner join ek_country c on c.id=a.country_id
where a.id=$value
             
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
  label, 
  concat (' ref=', reference)
) as label

from ek_product p 
inner join ek_product_reference pr on pr.product_id=p.id

where 
p.id=$value
", [], \PDO::FETCH_COLUMN);
                    break;
                case "auto.product_card":
                    $value = (int)$value;
                    $label = QuickPdo::fetch("
select 
concat (id, '. ', label) as label
from ek_product_card  
where 
id=$value
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