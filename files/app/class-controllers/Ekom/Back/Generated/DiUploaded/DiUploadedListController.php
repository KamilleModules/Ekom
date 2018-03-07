<?php

namespace Controller\Ekom\Back\Generated\DiUploaded;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class DiUploadedListController extends EkomBackSimpleFormListController
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
            'table' => "di_uploaded",
            'ric' => [
                "id",
            ],
            'label' => "uploaded",
            'labelPlural' => "uploadeds",
            'route' => "NullosAdmin_Ekom_Generated_DiUploaded_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "uploadeds",
                'breadcrumb' => "di_uploaded",
                'form' => "di_uploaded",
                'list' => "di_uploaded",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new uploaded",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_DiUploaded_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_DiUploaded_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_DiUploaded_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
