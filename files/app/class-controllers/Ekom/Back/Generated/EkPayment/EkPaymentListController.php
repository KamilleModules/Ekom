<?php

namespace Controller\Ekom\Back\Generated\EkPayment;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkPaymentListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		if ( array_key_exists ( "invoice_id", $_GET)) {        
            return $this->renderWithParent("ek_invoice", [
                "invoice_id" => $_GET["invoice_id"],
            ], [
                "invoice_id" => "id",
            ], [
                "invoice",
                "invoices",
            ], "NullosAdmin_Ekom_Generated_EkInvoice_List");
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "ek_payment",
            'ric' => [
                "id",
            ],
            'label' => "payment",
            'labelPlural' => "payments",
            'route' => "NullosAdmin_Ekom_Generated_EkPayment_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "payments",
                'breadcrumb' => "ek_payment",
                'form' => "ek_payment",
                'list' => "ek_payment",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new payment",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkPayment_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkPayment_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkPayment_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
