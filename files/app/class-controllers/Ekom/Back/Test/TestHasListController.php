<?php

namespace Controller\Ekom\Back\Test;


use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Module\Ekom\Api\Layer\SellerLayer;
use Module\Ekom\Back\User\EkomNullosUser;
use Module\Ekom\Utils\E;


//--------------------------------------------
// CHILD CONTROLLER
//--------------------------------------------
class TestHasListController extends EkomBackSimpleFormListController
{


    protected function updateDynamicTitle(&$title, $typeLabel, $id, $avatar, $langIso)
    {
        $title .= " for $typeLabel \"$avatar\" (#$id)";
    }


    public function render()
    {

        $langIso = EkomNullosUser::getEkomValue("lang_iso_code");

        //--------------------------------------------
        // foreach parentRic in uri
        //--------------------------------------------
        $seller_id = (array_key_exists("seller_id", $_GET)) ? $_GET['seller_id'] : null;
        $address_id = (array_key_exists("address_id", $_GET)) ? $_GET['address_id'] : null;
        // endforeach

        $avatar = SellerLayer::getRowAvatar($seller_id); // put all context keys if many


        //--------------------------------------------
        // DYNAMIC TITLE
        //--------------------------------------------
        $dynamicTitle = "seller-address";
        if ($seller_id) {
            $avatar = SellerLayer::getRowAvatar($seller_id);
            $this->updateDynamicTitle($dynamicTitle, "seller", $seller_id, $avatar, $langIso);
        } elseif ($address_id) { // put all parent ric keys if many (shop_has_product_has_provider)
            $avatar = SellerLayer::getRowAvatar($address_id);
            $this->updateDynamicTitle($dynamicTitle, "address", $address_id, $avatar, $langIso);
        }


        return $this->doRenderFormList([
            'title' => $dynamicTitle,
            'menuCurrentRoute' => "NullosAdmin_Ekom_Test_List",
            'breadcrumb' => "test_has",
            'form' => "test/test_has",
            'list' => "test/test_has",
            'ric' => [
                "seller_id",
                "address_id",
            ],
            /**
             * The back button is special and bound to the has logic, we will do this later..
             */
            "buttons" => [
                //--------------------------------------------
                //
                //--------------------------------------------
                //--------------------------------------------
                // foreach parentRic in uri
                //--------------------------------------------
                [
                    "label" => "Add a new seller-address for seller $avatar",
                    "icon" => "fa fa-plus-circle",
                    "link" => E::link("NullosAdmin_Ekom_TestHas_List") . "?form&seller_id=" . $seller_id,
                ],
                [
                    "label" => "Back to seller \"$avatar\" page",
                    "icon" => "fa fa-list",
                    "link" => E::link("NullosAdmin_Ekom_Test_List") . "?id=" . $seller_id, // note that this could have multiple keys (ric with shop_has_product_has_provider for instance)
                ],
                // end foreach
            ],
//            "buttonsList" => [
//                'label' => "Extra",
//                'items' => $items,
//            ],
            'context' => [
                //--------------------------------------------
                // foreach fk in uri
                //--------------------------------------------
                "seller_id" => $seller_id,
                "address_id" => $address_id,
                // endforeach
                "avatar" => $avatar,
            ],
        ]);
    }


}