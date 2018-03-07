<?php

namespace Controller\Ekom\Back\Generated\EkNewsletter;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkNewsletterListController extends EkomBackSimpleFormListController
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
            'table' => "ek_newsletter",
            'ric' => [
                "id",
            ],
            'label' => "newsletter",
            'labelPlural' => "newsletters",
            'route' => "NullosAdmin_Ekom_Generated_EkNewsletter_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "newsletters",
                'breadcrumb' => "ek_newsletter",
                'form' => "ek_newsletter",
                'list' => "ek_newsletter",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new newsletter",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkNewsletter_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkNewsletter_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkNewsletter_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
