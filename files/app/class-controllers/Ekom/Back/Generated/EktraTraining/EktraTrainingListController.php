<?php

namespace Controller\Ekom\Back\Generated\EktraTraining;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EktraTrainingListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		if ( array_key_exists ( "product_id", $_GET)) {        
            return $this->renderWithParent("ek_product", [
                "product_id" => $_GET["product_id"],
            ], [
                "product_id" => "id",
            ], [
                "product",
                "products",
            ], "NullosAdmin_Ekom_Generated_EkProduct_List");
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
            'table' => "ektra_training",
            'ric' => [
                "id",
            ],
            'label' => "training",
            'labelPlural' => "trainings",
            'route' => "NullosAdmin_Ekom_Generated_EktraTraining_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "trainings",
                'breadcrumb' => "ektra_training",
                'form' => "ektra_training",
                'list' => "ektra_training",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new training",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EktraTraining_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EktraTraining_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EktraTraining_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
