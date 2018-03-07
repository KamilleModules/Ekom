<?php

namespace Controller\Ekom\Back\Generated\EkShopConfiguration;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkShopConfigurationListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		if ( array_key_exists ( "shop_id", $_GET)) {        
            return $this->renderWithParent("ek_shop", [
                "shop_id" => $_GET["shop_id"],
            ], [
                "shop_id" => "id",
            ], [
                "shop",
                "shops",
            ], "NullosAdmin_Ekom_Generated_EkShop_List");
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "ek_shop_configuration",
            'ric' => [
                "shop_id",
            ],
            'label' => "shop configuration",
            'labelPlural' => "shop configurations",
            'route' => "NullosAdmin_Ekom_Generated_EkShopConfiguration_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "shop configurations",
                'breadcrumb' => "ek_shop_configuration",
                'form' => "ek_shop_configuration",
                'list' => "ek_shop_configuration",
                'ric' => [
                    "shop_id",
                ],

                "newItemBtnText" => "Add a new shop configuration",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkShopConfiguration_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkShopConfiguration_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkShopConfiguration_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
