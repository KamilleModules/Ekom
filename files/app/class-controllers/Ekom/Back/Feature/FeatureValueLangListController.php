<?php

namespace Controller\Ekom\Back\Feature;


use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Module\Ekom\Api\Layer\FeatureLayer;
use Module\Ekom\Api\Layer\ProductCardLayer;
use Module\Ekom\Back\User\EkomNullosUser;
use Module\Ekom\Utils\E;

class FeatureValueLangListController extends EkomBackSimpleFormListController
{
    public function render()
    {


        $langId = EkomNullosUser::getEkomValue("lang_id");

        $id = $this->getContextFromUrl('id');
        $avatar = FeatureLayer::getFeatureValueRepresentationById($id, $langId);


        return $this->doRenderFormList([
            'title' => "Translations for product feature value \"$avatar\"",
            'menuCurrentRoute' => "NullosAdmin_Ekom_FeatureValue_List",
            'breadcrumb' => "feature_value_lang",
            'form' => "feature_value_lang",
            'list' => "feature_value_lang",
            'ric' => [
                "feature_value_id",
                "lang_id",
            ],
            "buttons" => [
                [
                    "label" => "Back to product feature value \"$avatar\" page",
                    "icon" => "fa fa-list",
                    "link" => E::link("NullosAdmin_Ekom_FeatureValue_List") . "?id=" . $id,
                ],
            ],
            'newItemBtnText' => "Add a new translation for product feature value \"$avatar\"",
            'newItemBtnLink' => E::link("NullosAdmin_Ekom_FeatureValueLang_List") . "?form&id=" . $id,
            'context' => [
                "id" => $id,
            ],
        ]);
    }


}