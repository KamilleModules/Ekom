<?php


namespace _controllerNamespace_;

use Controller\NullosAdmin\Back\NullosMorphicController;
use Kamille\Utils\Claws\ClawsWidget;

class _controllerClassname_ extends NullosMorphicController
{


    public function render()
    {
        $this->prepareClaws();


        if (array_key_exists("id", $_GET)) {

            $this->setSelectedLeftMenuItemByRoute("Ekom_Users_User_List");

            $orderId = $_GET['id'];
//            $orderModel = OrderInfoModel::getModelByOrderId($orderId);




            $this->pageTop([
                "title" => "Informations sur le client XXX",
                "breadcrumbs" => [
                    [
                        "label" => "ek_order",
                    ]
                ],
            ]);

//            $this->topStats(function (TopStatObject $topStat) use($model){
//                $topStat->addElement([
//                    "label" => "Date",
//                    "icon" => "fa fa-calendar",
//                    "theme" => "purple",
//                    "content" => $model['date'],
//                ]);
//                $topStat->addElement([
//                    "label" => "Total",
//                    "icon" => "fa fa-money",
//                    "theme" => "green",
//                    "content" => $model['amount'],
//                ]);
//                $topStat->addElement([
//                    "label" => "Messages",
//                    "icon" => "fa fa-comments-o",
//                    "theme" => "red",
//                    "content" => '0',
//                ]);
//                $topStat->addElement([
//                    "label" => "Produits",
//                    "icon" => "fa fa-book",
//                    "theme" => "blue",
//                    "content" => $model['cart_quantity'],
//                ]);
//            });

            $claws = $this->getClaws()
                ->setLayout("admin/2-cols")
                ->setWidget("maincontent.orderGeneralInfoBlock", ClawsWidget::create()
                    ->setTemplate("NullosAdmin/Main/InfoList/prototype")
                    ->setConf([
                        'title' => "Colonne dans main",
                    ])
                )
                ->setWidget("maincontent_left.orderCarrierInfoBlock", ClawsWidget::create()
                    ->setTemplate("Ekom/Main/InfoList/carriers")
                    ->setTemplate("NullosAdmin/Main/InfoList/prototype")
                    ->setConf([
                        'title' => "Colonne gauche",
                    ])
                )
                ->setWidget("maincontent_right.orderStatusInfoBlock", ClawsWidget::create()
                    ->setTemplate("Ekom/Main/InfoList/order_status")
                    ->setTemplate("NullosAdmin/Main/InfoList/prototype")
                    ->setConf([
                        'title' => "Colonne droite",
                    ])
                )
                ->setWidget("maincontent_bottom.orderPaymentOccurrences", ClawsWidget::create()
                    ->setTemplate("Ekom/Main/InfoList/invoice")
                    ->setTemplate("NullosAdmin/Main/InfoList/prototype")
                    ->setConf([
                        'title' => "Colonne dans bottom",
                    ])
                )
            ;



//            $params= [
//                'order_id' => $orderId
//            ];
//            Hooks::call("Ekom_Controller_Orders_OrderInfoController", $claws, $params);

        } else {
            $this->errorTemplate("Aucun argument passé dans l'url", "Erreur");
        }
        return parent::doRenderClaws();
    }


}


