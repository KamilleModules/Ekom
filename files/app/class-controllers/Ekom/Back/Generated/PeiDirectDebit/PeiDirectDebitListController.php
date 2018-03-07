<?php

namespace Controller\Ekom\Back\Generated\PeiDirectDebit;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class PeiDirectDebitListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		if ( array_key_exists ( "order_id", $_GET)) {        
            return $this->renderWithParent("ek_order", [
                "order_id" => $_GET["order_id"],
            ], [
                "order_id" => "id",
            ], [
                "order",
                "orders",
            ], "NullosAdmin_Ekom_Generated_EkOrder_List");
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "pei_direct_debit",
            'ric' => [
                "id",
            ],
            'label' => "direct debit",
            'labelPlural' => "direct debits",
            'route' => "NullosAdmin_Ekom_Generated_PeiDirectDebit_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "direct debits",
                'breadcrumb' => "pei_direct_debit",
                'form' => "pei_direct_debit",
                'list' => "pei_direct_debit",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new direct debit",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_PeiDirectDebit_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_PeiDirectDebit_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_PeiDirectDebit_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
