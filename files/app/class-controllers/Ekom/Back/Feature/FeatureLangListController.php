<?php

namespace Controller\Ekom\Back\Feature;


use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Module\Ekom\Api\Layer\FeatureLayer;
use Module\Ekom\Api\Layer\ProductCardLayer;
use Module\Ekom\Back\User\EkomNullosUser;
use Module\Ekom\Utils\E;

class FeatureLangListController extends EkomBackSimpleFormListController
{
    public function render()
    {


        $langId = EkomNullosUser::getEkomValue("lang_id");

        $id = $this->getContextFromUrl('id');
        $avatar = FeatureLayer::getRepresentationById($id, $langId);

        return $this->doRenderFormList([
            'title' => "Translations for product feature \"$avatar\"",
            'menuCurrentRoute' => "NullosAdmin_Ekom_Feature_List",
            'breadcrumb' => "feature_lang",
            'form' => "feature_lang",
            'list' => "feature_lang",
            'ric' => [
                "feature_id",
                "lang_id",
            ],
            "buttons" => [
                [
                    "label" => "Back to product feature \"$avatar\" page",
                    "icon" => "fa fa-list",
                    "link" => E::link("NullosAdmin_Ekom_Feature_List") . "?id=" . $id,
                ],
            ],
            'newItemBtnText' => "Add a new translation for product feature \"$avatar\"",
            'newItemBtnLink' => E::link("NullosAdmin_Ekom_FeatureLang_List") . "?form&id=" . $id,
            'context' => [
                "id" => $id,
            ],
        ]);
    }


}