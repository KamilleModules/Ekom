<?php

namespace Controller\Ekom\Back\Generated\DiGroupHasPage;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class DiGroupHasPageListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_DiGroupHasPage_List";

        //--------------------------------------------
        // CONTEXT PATTERN
        //--------------------------------------------            
		$group_id = $this->getContextFromUrl('group_id');
		$table = "di_group_has_page";
		$context = [
			"group_id" => $group_id,
		];

        $avatar = null;
        Hooks::call("Ekom_Back_getElementAvatar", $avatar, $table, $context);
        if (null === $avatar) {
            $repr = OrmToolsHelper::getRepresentativeColumn("di_group");
            $avatar = QuickPdo::fetch("
select $repr from `di_group` where id=:group_id 
            ", [
				"group_id" => $group_id,
            ], \PDO::FETCH_COLUMN);
        }
            

        return $this->doRenderFormList([
            'title' => "Pages for group \"$avatar\"",
            'breadcrumb' => "di_group_has_page",
            'form' => "di_group_has_page",
            'list' => "di_group_has_page",
            'ric' => [
                'group_id',
                'page_id',
            ],
            
            "newItemBtnText" => "Add a new page for group \"$avatar\"",
            "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_DiGroupHasPage_List") . "?form&group_id=$group_id",
            
            "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_DiGroup_List",             
            "buttons" => [
                [
                    "label" => "Back to group \"$avatar\" page",
                    "icon" => "fa fa-list",
                    "link" => E::link("NullosAdmin_Ekom_Generated_DiGroup_List") . "?id=$group_id",
                ],
            ],
            "context" => [
            	"group_id" => $group_id,
				"avatar" => $avatar

            ],            
            
        ]);
    }


}