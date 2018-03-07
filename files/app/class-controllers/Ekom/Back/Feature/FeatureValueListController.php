<?php

namespace Controller\Ekom\Back\Feature;


use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Module\Ekom\Api\Layer\UserLayer;
use Module\Ekom\Utils\E;

class FeatureValueListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        return $this->doRenderFormList([
            'title' => "Product feature values",
//            'menuCurrentRoute' => "NullosAdmin_Ekom_User_List",
            'breadcrumb' => "feature_value",
            'form' => "feature_value",
            'list' => "feature_value",
            'ric' => [
                "id",
            ],
            'newItemBtnText' => "Add a new product feature value",
            'newItemBtnRoute' => "NullosAdmin_Ekom_FeatureValue_List",
        ]);
    }


}