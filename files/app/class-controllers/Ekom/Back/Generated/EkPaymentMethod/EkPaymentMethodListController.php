<?php

namespace Controller\Ekom\Back\Generated\EkPaymentMethod;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkPaymentMethodListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "ek_payment_method",
            'ric' => [
                "id",
            ],
            'label' => "payment method",
            'labelPlural' => "payment methods",
            'route' => "NullosAdmin_Ekom_Generated_EkPaymentMethod_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "payment methods",
                'breadcrumb' => "ek_payment_method",
                'form' => "ek_payment_method",
                'list' => "ek_payment_method",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new payment method",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkPaymentMethod_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkPaymentMethod_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkPaymentMethod_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
