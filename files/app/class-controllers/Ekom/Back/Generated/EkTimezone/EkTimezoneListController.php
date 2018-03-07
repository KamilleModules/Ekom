<?php

namespace Controller\Ekom\Back\Generated\EkTimezone;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkTimezoneListController extends EkomBackSimpleFormListController
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
            'table' => "ek_timezone",
            'ric' => [
                "id",
            ],
            'label' => "timezone",
            'labelPlural' => "timezones",
            'route' => "NullosAdmin_Ekom_Generated_EkTimezone_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "timezones",
                'breadcrumb' => "ek_timezone",
                'form' => "ek_timezone",
                'list' => "ek_timezone",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new timezone",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkTimezone_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkTimezone_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkTimezone_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
