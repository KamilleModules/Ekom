<?php

namespace Controller\Ekom\Back\Tag;


use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;

class TagListController extends EkomBackSimpleFormListController
{
    public function render()
    {
        return $this->doRenderFormList([
            'title' => "Tag",
            'breadcrumb' => "tag",
            'form' => "tag",
            'list' => "tag",
            'ric' => "id",
            'newItemBtnText' => "Add a new tag",
            'newItemBtnRoute' => "NullosAdmin_Ekom_Tag_List",
        ]);
    }




}