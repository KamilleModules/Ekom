<?php

namespace Controller\Ekom\Back\Generated\EktraDateRange;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EktraDateRangeListController extends EkomBackSimpleFormListController
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
            'table' => "ektra_date_range",
            'ric' => [
                "id",
            ],
            'label' => "date range",
            'labelPlural' => "date ranges",
            'route' => "NullosAdmin_Ekom_Generated_EktraDateRange_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "date ranges",
                'breadcrumb' => "ektra_date_range",
                'form' => "ektra_date_range",
                'list' => "ektra_date_range",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new date range",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EktraDateRange_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EktraDateRange_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EktraDateRange_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
