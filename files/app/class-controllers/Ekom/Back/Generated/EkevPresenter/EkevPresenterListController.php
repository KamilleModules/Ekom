<?php

namespace Controller\Ekom\Back\Generated\EkevPresenter;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkevPresenterListController extends EkomBackSimpleFormListController
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
            'table' => "ekev_presenter",
            'ric' => [
                "id",
            ],
            'label' => "presenter",
            'labelPlural' => "presenters",
            'route' => "NullosAdmin_Ekom_Generated_EkevPresenter_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "presenters",
                'breadcrumb' => "ekev_presenter",
                'form' => "ekev_presenter",
                'list' => "ekev_presenter",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new presenter",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkevPresenter_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkevPresenter_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkevPresenter_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
