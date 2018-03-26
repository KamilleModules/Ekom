<?php


namespace Module\Ekom\Back\Helper;

use Core\Services\Hooks;
use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Utils\Claws\ClawsInterface;
use Kamille\Utils\Claws\ClawsWidget;
use Models\AdminSidebarMenu\Lee\LeeAdminSidebarMenuModel;
use Models\AdminSidebarMenu\Lee\Objects\Item;
use Models\AdminSidebarMenu\Lee\Objects\Section;
use Module\Ekom\Api\Layer\CurrencyLayer;
use Module\Ekom\Api\Layer\LangLayer;
use Module\Ekom\Api\Layer\ShopLayer;
use Module\Ekom\Back\User\EkomNullosUser;
use Module\Ekom\Back\Util\QuickStartWizard\QuickStartWizard;
use Module\Ekom\Back\WidgetModel\ContextBar\ContextBarWidgetModel;
use Module\Ekom\Back\WidgetModel\Dashboard\DefaultDashboardModel;
use Module\NullosAdmin\Helper\NullosGuiEnvironment;
use Module\NullosAdmin\Utils\N;
use QuickPdo\QuickPdo;

class BackHooksHelper
{


    public static function ApplicationMorphicGenerator_getMorphicGeneratorTranslationFiles(array &$info)
    {
        $lang = $info['lang'];
        switch ($lang) {
            case "fra":
                $info['tables']["ek_"] = __DIR__ . "/../../assets/morphic/fra/ekom-morphic-tables.xml";
                $info['cols']["ek_"] = __DIR__ . "/../../assets/morphic/fra/ekom-morphic-cols.xml";
                break;
            default:
                break;
        }
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


        $section
            //--------------------------------------------
            // CATALOG
            //--------------------------------------------
            ->addItem(Item::create()
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
                    ->setLink(N::link("Ekom_Catalog_Product_List"))
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
                        ->setLink(N::link("Ekom_Catalog_Category_List"))
                    )
                    ->addItem(Item::create()
                        ->setActive(true)
                        ->setName("catalog_categories_tree")
                        ->setLabel("Arbre des catégories")
//                    ->setIcon("fa fa-dot-circle-o")
                        ->setLink(N::link("Ekom_Catalog_CategoryTree_List"))
                    )
                )
                ->addItem(Item::create()
                    ->setActive(true)
                    ->setName("catalog_bundles")
                    ->setLabel("Packs")
                    ->setLink(N::link("Ekom_Catalog_Bundle_List"))
                )
                ->addItem(Item::create()
                    ->setActive(true)
                    ->setName("catalog_product_groups")
                    ->setLabel("Groupes de produit")
                    ->setLink(N::link("Ekom_Catalog_ProductGroup_List"))
                )
                ->addItem(Item::create()
                    ->setActive(true)
                    ->setName("catalog_attributes")
                    ->setLabel("Attributs")
                    ->setLink(N::link("Ekom_Catalog_ProductAttribute_List"))
                )
                ->addItem(Item::create()
                    ->setActive(true)
                    ->setName("catalog_manufacturers")
                    ->setLabel("Fabricants")
                    ->setLink(N::link("Ekom_Catalog_Manufacturer_List"))
                )
                ->addItem(Item::create()
                    ->setActive(true)
                    ->setName("catalog_providers")
                    ->setLabel("Fournisseurs")
                    ->setLink(N::link("Ekom_Catalog_Provider_List"))
                )
                ->addItem(Item::create()
                    ->setActive(true)
                    ->setName("catalog_tags")
                    ->setLabel("Tags")
                    ->setLink(N::link("Ekom_Catalog_Tag_List"))
                )
            )
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
                    ->setLink(N::link("Ekom_Orders_Order_List"))
                )
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
                ->setLink(N::link("Ekom_Utils_CacheManager"))
            );


        $section
            ->addItem($utilsItem);


        $menuItems = [
            'Ekom.utils' => $utilsItem,
        ];
        Hooks::call("Ekom_decorateLeftMenu", $menuItems);

    }

    public static function NullosAdmin_prepareClaws(ClawsInterface $claws, $type = null)
    {


        if (false === "old") {

            //--------------------------------------------
            // ENSURE THAT CONTEXT VARS ARE PROPERLY SET
            //--------------------------------------------
            /**
             * The currency, lang, and shop must be defined
             * prior to any other actions.
             */
            $message = null;
            if (false === QuickStartWizard::checkApp($message)) {
                NullosGuiEnvironment::addNotification($message, "error");
            }
//        $model = ContextBarWidgetModel::getModel();
//        $claws
//            ->setWidget("topbar_right.ekomContextBar", ClawsWidget::create()
//                ->setTemplate('NullosAdmin/TopBar/EkomContextBar/default')
//                ->setConf($model), "last"
//            );
        }


        //--------------------------------------------
        // HOME SPECIFIC
        //--------------------------------------------
        if ('home' === $type) {

            $claws->removeWidget("maincontent.body");

            $model = DefaultDashboardModel::getModel();
            $claws
                ->setWidget("maincontent.pageTop", ClawsWidget::create()
                    ->setTemplate('NullosAdmin/Main/PageTop/default')
                    ->setConf([
                        "breadcrumbs" => [
                            [
                                'label' => 'dashboard',
                                'link' => '',
                            ]
                        ],
                        "title" => "Tableau de bord",
                        "buttons" => [],
                        "buttonsList" => [],
                    ])
                )
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

from ek_product 

where 
id=$value
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