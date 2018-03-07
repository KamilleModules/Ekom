<?php

namespace Controller\Ekom\Back\Generated\EkProductComment;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkProductCommentListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkProductComment_List";


        return $this->doRenderFormList([
            'title' => "Product comments for this shop",
            'breadcrumb' => "ek_product_comment",
            'form' => "ek_product_comment",
            'list' => "ek_product_comment",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Product comment",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}