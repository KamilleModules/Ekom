<?php

namespace Controller\Ekom\Back\Generated\LocationHasHotel;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class LocationHasHotelListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_LocationHasHotel_List";

        //--------------------------------------------
        // CONTEXT PATTERN
        //--------------------------------------------            
		$location_id = $this->getContextFromUrl('location_id');
		$table = "ekev_location_has_hotel";
		$context = [
			"location_id" => $location_id,
		];

        $avatar = null;
        Hooks::call("Ekom_Back_getElementAvatar", $avatar, $table, $context);
        if (null === $avatar) {
            $repr = OrmToolsHelper::getRepresentativeColumn("ekev_location");
            $avatar = QuickPdo::fetch("
select $repr from `ekev_location` where id=:location_id 
            ", [
				"location_id" => $location_id,
            
            ], \PDO::FETCH_COLUMN);
        }
            

        return $this->doRenderFormList([
            'title' => "Hotels for location \"$avatar\"",
            'breadcrumb' => "location_has_hotel",
            'form' => "location_has_hotel",
            'list' => "location_has_hotel",
            'ric' => [
                'location_id',
                'hotel_id',
            ],
            
            "newItemBtnText" => "Add a new hotel for location \"$avatar\"",
            "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_LocationHasHotel_List") . "?form&location_id=$location_id",
            
            "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkevLocation_List",             
            "buttons" => [
                [
                    "label" => "Back to location \"$avatar\" page",
                    "icon" => "fa fa-list",
                    "link" => E::link("NullosAdmin_Ekom_Generated_Location_List") . "?id=$location_id",
                ],
            ],
            "context" => [
            	"location_id" => $location_id,
				"avatar" => $avatar

            ],            
            
        ]);
    }


}