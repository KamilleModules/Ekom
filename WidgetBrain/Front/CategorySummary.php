<?php


namespace Module\Ekom\WidgetBrain\Front;


use Module\Ekom\Api\EkomApi;
use Module\Ekom\WidgetBrain\WidgetBrain;

class CategorySummary extends WidgetBrain
{

    public function getModel($categoryId)
    {
        $api = EkomApi::inst()->categoryLayer();
        $subCats = $api->getSubCategoriesById($categoryId, 0);
        $info = $api->getCategoryInfoById($categoryId);
        return [
            'label' => $info['label'],
            'description' => $info['description'],
            'cats' => $subCats,
        ];
    }

}

