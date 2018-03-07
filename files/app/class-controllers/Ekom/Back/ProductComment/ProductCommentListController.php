<?php

namespace Controller\Ekom\Back\ProductComment;


use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;

class ProductCommentListController extends EkomBackSimpleFormListController
{
    public function render()
    {
        return $this->doRenderFormList([
            'title' => "Product comment",
            'breadcrumb' => "product_comment",
            'form' => "product_comment",
            'list' => "product_comment",
            'ric' => "id",
            'newItemBtnText' => "Add a new product comment",
            'newItemBtnRoute' => "NullosAdmin_Ekom_ProductComment_List",
        ]);
    }




}