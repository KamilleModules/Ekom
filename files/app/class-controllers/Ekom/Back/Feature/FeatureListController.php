<?php

namespace Controller\Ekom\Back\Feature;


use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Module\Ekom\Api\Layer\UserLayer;
use Module\Ekom\Utils\E;

class FeatureListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        return $this->doRenderFormList([
            'title' => "Product features",
//            'menuCurrentRoute' => "NullosAdmin_Ekom_User_List",
            'breadcrumb' => "feature",
            'form' => "feature",
            'list' => "feature",
            'ric' => [
                "id",
            ],
            'newItemBtnText' => "Add a new product feature",
            'newItemBtnRoute' => "NullosAdmin_Ekom_Feature_List",
        ]);
    }


}