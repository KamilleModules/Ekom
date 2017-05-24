<?php


namespace Module\Ekom\Api\Layer;


use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Module\Ekom\Api\EkomApi;
use QuickPdo\QuickPdo;

class ProductLayer
{


    /**
     * @return false|int, the id of the product card which slug was given, or false if there is no matching product card.
     */
    public function getProductCardIdBySlug($slug)
    {
        $shopId = ApplicationRegistry::get("ekom.front.shop_id");
        $langId = ApplicationRegistry::get("ekom.front.lang_id");
        if (false !== ($productCardId = EkomApi::inst()->shopHasProductCardLang()->readColumn("product_card_id", [
                ["shop_id", '=', $shopId],
                ["lang_id", '=', $langId],
                ["slug", '=', $slug],
            ]))
        ) {
            return $productCardId;
        }
        return EkomApi::inst()->productCardLang()->readColumn("product_card_id", [
            ["slug", '=', $slug],
            ["lang_id", '=', $langId],
        ]);
    }


    /**
     * @param $cardId
     * @return false|mixed
     */
    public function getProductBoxModelByCardId($cardId)
    {


        $model = [];

        $shopId = ApplicationRegistry::get("ekom.front.shop_id");
        $langId = ApplicationRegistry::get("ekom.front.lang_id");

        $cardId = (int)$cardId;
        $shopId = (int)$shopId;
        $langId = (int)$langId;


        if (false !== ($row = QuickPdo::fetch("
select
 
sl.label,
sl.slug,
sl.description,
s.product_id,
s.active,
l.label as default_label,
l.description as default_description,
l.slug as default_slug

from ek_shop_has_product_card_lang sl 
inner join ek_shop_has_product_card s on s.shop_id=sl.shop_id and s.product_card_id=sl.product_card_id
inner join ek_product_card_lang l on l.product_card_id=sl.product_card_id and l.lang_id=sl.lang_id

where s.shop_id=$shopId 
and s.product_card_id=$cardId and sl.lang_id=$langId 
"))
        ) {

            if ('1' === $row['active']) {
                a($row);
                az($cardId);





            } else {
                /**
                 * product card not associated with this shop/lang.
                 */
                $model['errorCode'] = "inactive";
                $model['errorTitle'] = "Product card not active";
                $model['errorMessage'] = "This product card is not active for this shop, sorry";
            }
        } else {
            /**
             * product card not associated with this shop/lang.
             */
            $model['errorCode'] = "noAssociation";
            $model['errorTitle'] = "Product card not associated";
            $model['errorMessage'] = "This product card is not associated with this shop, sorry";
        }


        $uri = "/theme/" . ApplicationParameters::get("theme");
        $names = [
            "balance-board.jpg",
            "balance-board-logo.jpg",
            "balance-board-demo.jpg",
            "balance-board-arriere.jpg",
            "balance-board.jpg",
            "balance-board-logo.jpg",
            "balance-board-demo.jpg",
            "balance-board-arriere.jpg",
        ];


        /**
         * The keys of the images are fileNames (like "balance-board.jpg" for instance)
         */
        $images = [];
        foreach ($names as $fileName) {
            $images[$fileName] = [
                'thumb' => $uri . "/img/products/balance-board/thumb/$fileName",
                'small' => $uri . "/img/products/balance-board/small/$fileName",
                'large' => $uri . "/img/products/balance-board/large/$fileName",
            ];
        }

        $boxConf = [
            "images" => $images,
            "defaultImage" => "balance-board.jpg",
            "label" => "Balance Board",
            "ref" => "1436",
            "description" => "Plateau de freeman en bois idéal pour travailler les muscles stabilisateurs, l'équilibre et la coordination. Ultra résistant grâce à son bois robuste, le plateau dispose d'une surface antidérapante.",
            /**
             * Is used by the widget to assign visual cues (for instance success color) to the stockText
             * List of available types will be defined later.
             */
            "stockType" => "stockAvailable",
            "stockText" => "En stock",
            "price" => "12.69 €", // note that price includes currency (and relevant formatting)
            // if type is null, the price is not discounted,
            // otherwise, the discount_ data help displaying the right discounted price
            "discount_type" => null,
            "discount_amount" => "0",
            "discount_price" => "0",
            "attributes" => [
                'weight' => [
                    "label" => "poids",
                    "selected" => "1 kg",
                    "values" => [
                        "0.5 kg",
                        "1 kg",
                        "2 kg",
                        "3 kg",
                        "4 kg",
                        "5 kg",
                    ],
                ],
            ],
            //--------------------------------------------
            // EXTENSION: SPECIFIC TO SOME PLUGINS
            // consider using namespace_varName notation
            //--------------------------------------------
            // rating
            "rating_amount" => "80", // percent
            "rating_nbVotes" => "6",
            // video
            "video_sources" => [
                "/video/Larz Rocking Leaderfit Paris 2017 Step V2.mp4" => "video/mp4",
            ],
        ];

        return $boxConf;
    }

}