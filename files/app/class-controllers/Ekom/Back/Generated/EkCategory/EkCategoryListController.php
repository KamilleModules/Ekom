<?php

namespace Controller\Ekom\Back\Generated\EkCategory;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkCategoryListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		if ( array_key_exists ( "category_id", $_GET)) {        
            return $this->renderWithParent("ek_category", [
                "category_id" => $_GET["category_id"],
            ], [
                "category_id" => "id",
            ], [
                "category",
                "categories",
            ], "NullosAdmin_Ekom_Generated_EkCategory_List");
		} elseif ( array_key_exists ( "shop_id", $_GET)) {        
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
            'table' => "ek_category",
            'ric' => [
                "id",
            ],
            'label' => "category",
            'labelPlural' => "categories",
            'route' => "NullosAdmin_Ekom_Generated_EkCategory_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "categories",
                'breadcrumb' => "ek_category",
                'form' => "ek_category",
                'list' => "ek_category",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new category",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkCategory_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkCategory_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkCategory_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
