<?php

namespace Controller\Ekom\Back\Generated\EkevPresenterGroupHasPresenter;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkevPresenterGroupHasPresenterListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkevPresenterGroupHasPresenter_List";

        //--------------------------------------------
        // CONTEXT PATTERN
        //--------------------------------------------            
		$presenter_group_id = $this->getContextFromUrl('presenter_group_id');
		$table = "ekev_presenter_group_has_presenter";
		$context = [
			"presenter_group_id" => $presenter_group_id,
		];

        $avatar = null;
        Hooks::call("Ekom_Back_getElementAvatar", $avatar, $table, $context);
        if (null === $avatar) {
            $repr = OrmToolsHelper::getRepresentativeColumn("ekev_presenter_group");
            $avatar = QuickPdo::fetch("
select $repr from `ekev_presenter_group` where id=:presenter_group_id 
            ", [
				"presenter_group_id" => $presenter_group_id,
            
            ], \PDO::FETCH_COLUMN);
        }
            

        return $this->doRenderFormList([
            'title' => "Presenters for presenter group \"$avatar\"",
            'breadcrumb' => "ekev_presenter_group_has_presenter",
            'form' => "ekev_presenter_group_has_presenter",
            'list' => "ekev_presenter_group_has_presenter",
            'ric' => [
                'presenter_group_id',
                'presenter_id',
            ],
            
            "newItemBtnText" => "Add a new presenter for presenter group \"$avatar\"",
            "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkevPresenterGroupHasPresenter_List") . "?form&presenter_group_id=$presenter_group_id",
            
            "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkevPresenterGroup_List",             
            "buttons" => [
                [
                    "label" => "Back to presenter group \"$avatar\" page",
                    "icon" => "fa fa-list",
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkevPresenterGroup_List") . "?id=$presenter_group_id",
                ],
            ],
            "context" => [
            	"presenter_group_id" => $presenter_group_id,
				"avatar" => $avatar

            ],            
            
        ]);
    }


}