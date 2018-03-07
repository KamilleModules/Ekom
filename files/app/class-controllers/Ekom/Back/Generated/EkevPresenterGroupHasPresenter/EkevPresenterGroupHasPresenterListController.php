<?php

namespace Controller\Ekom\Back\Generated\EkevPresenterGroupHasPresenter;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkevPresenterGroupHasPresenterListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		if ( array_key_exists ( "presenter_id", $_GET)) {        
            return $this->renderWithParent("ekev_presenter", [
                "presenter_id" => $_GET["presenter_id"],
            ], [
                "presenter_id" => "id",
            ], [
                "presenter",
                "presenters",
            ], "NullosAdmin_Ekom_Generated_EkevPresenter_List");
		} elseif ( array_key_exists ( "presenter_group_id", $_GET)) {        
            return $this->renderWithParent("ekev_presenter_group", [
                "presenter_group_id" => $_GET["presenter_group_id"],
            ], [
                "presenter_group_id" => "id",
            ], [
                "presenter group",
                "presenter groups",
            ], "NullosAdmin_Ekom_Generated_EkevPresenterGroup_List");
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "ekev_presenter_group_has_presenter",
            'ric' => [
                "presenter_group_id",
				"presenter_id",
            ],
            'label' => "presenter group-presenter",
            'labelPlural' => "presenter group-presenters",
            'route' => "NullosAdmin_Ekom_Generated_EkevPresenterGroupHasPresenter_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "presenter group-presenters",
                'breadcrumb' => "ekev_presenter_group_has_presenter",
                'form' => "ekev_presenter_group_has_presenter",
                'list' => "ekev_presenter_group_has_presenter",
                'ric' => [
                    "presenter_group_id",
					"presenter_id",
                ],

                "newItemBtnText" => "Add a new presenter group-presenter",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkevPresenterGroupHasPresenter_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkevPresenterGroupHasPresenter_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkevPresenterGroupHasPresenter_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
