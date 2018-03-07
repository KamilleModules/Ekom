<?php

namespace Controller\Ekom\Back\Generated\ProductComment;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class ProductCommentListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_ProductComment_List";


        return $this->doRenderFormList([
            'title' => "Product comments for this shop",
            'breadcrumb' => "product_comment",
            'form' => "product_comment",
            'list' => "product_comment",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Product comment",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}