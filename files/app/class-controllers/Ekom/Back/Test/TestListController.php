<?php

namespace Controller\Ekom\Back\Test;


use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Module\Ekom\Utils\E;

class TestListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        return $this->doRenderFormList([
            'title' => "Test",
            'breadcrumb' => "test",
            'form' => "test/test",
            'list' => "test/test",
            'ric' => [
                'id',
            ],
            "buttons" => [
                //--------------------------------------------
                //
                //--------------------------------------------
                [
                    "label" => "Add a new seller test item",
                    "icon" => "fa fa-plus-circle",
                    "link" => E::link("NullosAdmin_Ekom_Test_List") . "?form",
                ],
            ],
        ]);
    }


}