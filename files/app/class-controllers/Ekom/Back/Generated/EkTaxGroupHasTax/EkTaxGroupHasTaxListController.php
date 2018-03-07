<?php

namespace Controller\Ekom\Back\Generated\EkTaxGroupHasTax;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkTaxGroupHasTaxListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		if ( array_key_exists ( "tax_id", $_GET)) {        
            return $this->renderWithParent("ek_tax", [
                "tax_id" => $_GET["tax_id"],
            ], [
                "tax_id" => "id",
            ], [
                "tax",
                "taxes",
            ], "NullosAdmin_Ekom_Generated_EkTax_List");
		} elseif ( array_key_exists ( "tax_group_id", $_GET)) {        
            return $this->renderWithParent("ek_tax_group", [
                "tax_group_id" => $_GET["tax_group_id"],
            ], [
                "tax_group_id" => "id",
            ], [
                "tax group",
                "tax groups",
            ], "NullosAdmin_Ekom_Generated_EkTaxGroup_List");
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "ek_tax_group_has_tax",
            'ric' => [
                "tax_group_id",
				"tax_id",
            ],
            'label' => "tax group-tax",
            'labelPlural' => "tax group-taxes",
            'route' => "NullosAdmin_Ekom_Generated_EkTaxGroupHasTax_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "tax group-taxes",
                'breadcrumb' => "ek_tax_group_has_tax",
                'form' => "ek_tax_group_has_tax",
                'list' => "ek_tax_group_has_tax",
                'ric' => [
                    "tax_group_id",
					"tax_id",
                ],

                "newItemBtnText" => "Add a new tax group-tax",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkTaxGroupHasTax_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkTaxGroupHasTax_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkTaxGroupHasTax_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
