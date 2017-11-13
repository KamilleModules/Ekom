<?php


namespace Module\Ekom\Model\Front;


use Bat\UriTool;
use Core\Services\Hooks;
use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Services\XLog;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Entity\ProductBoxEntityUtil;
use Module\Ekom\Api\Layer\FeatureLayer;
use Module\Ekom\Api\Layer\ProductBoxLayer;
use Module\Ekom\Api\Layer\ProductLayer;
use Module\Ekom\Utils\E;

class ProductPageModel
{


    /**
     * We recommend not to cache this method
     * --------------------------------------
     * Beware: if you want to cache this method's output,
     * - you need to encapsulate the list params of the product comments
     * - you need to manually trigger the interactions (see interactions section below)
     * - you need to take into account the case of connected user/not connected user
     *
     * @param $reference
     * @return array
     */
    public static function getModelByProductReference($reference)
    {
        $head = []; // product box model
        $tail = []; // tail box model


        if (false !== ($productId = ProductLayer::getProductIdByRef($reference))) {
            $productDetails = ProductBoxEntityUtil::filterProductDetails($_GET);
            $head = ProductBoxLayer::getProductBoxByProductId($productId, $productDetails);
            //--------------------------------------------
            // PREPARING TAIL
            //--------------------------------------------
            $tail = self::getProductPageTailModel($head);
//            Hooks::call("Ekom_decorateProductBoxClaws", $productPageModel); // todo: ?

        } else {
            if (true === ApplicationParameters::get("debug")) {
                XLog::debug("[Ekom module] - ProductPageModel: product not found with ref: $reference");
            }
            $head = [
                'errorCode' => 'productRefNotFound',
                'errorTitle' => "Product reference not found",
                'errorMessage' => "The product with reference $reference doesn't exist in our database",
            ];
        }

        return [
            'head' => $head,
            'tail' => $tail,
        ];
    }


    public static function getModelByProductId($productId)
    {
        $productDetails = ProductBoxEntityUtil::filterProductDetails($_GET);
        $head = ProductBoxLayer::getProductBoxByProductId($productId, $productDetails);
        $tail = self::getProductPageTailModel($head);

        return [
            'head' => $head,
            'tail' => $tail,
        ];
    }



    //--------------------------------------------
    //
    //--------------------------------------------
    private static function getProductPageTailModel(array $head)
    {
        $tail = [];
        $cardId = $head['card_id'];
        $productId = $head['product_id'];


        //--------------------------------------------
        // FEATURES (properties)
        //--------------------------------------------
        $features = FeatureLayer::getFeaturesModelByProductId($productId);
        $technicalDescription = "";
        if (count($features) > 0) {
            $technicalDescription = $features[0]['technical_description'];
        }
        $featuresModel = [
            "technical_description" => $technicalDescription,
            "features" => $features,
            'seller' => $head['seller'],
        ];


        //--------------------------------------------
        // COMMENTS
        //--------------------------------------------
        $page = (array_key_exists('c-page', $_GET)) ? $_GET['c-page'] : 1;
        $sort = (array_key_exists('c-sort', $_GET)) ? $_GET['c-sort'] : 'date';
        $sortDir = (array_key_exists('c-sort-dir', $_GET)) ? $_GET['c-sort-dir'] : 'desc';

        $options = [
            'page' => $page,
            'sort' => $sort,
            'sort-dir' => $sortDir,
        ];
        $comments = EkomApi::inst()->productCommentLayer()->getCommentsByProductId($head['product_id'], null, $options);
        $hash = "widget-product-comments";

        $nbComments = count($comments);
        $sum = 0;
        $avg = 0;
        foreach ($comments as $c) {
            $sum += $c['rating'];
        }
        if ($nbComments > 0) {
            $avg = $sum / $nbComments;
        }
        $uriPattern = UriTool::uri(null, [
                'c-sort' => '_CSORT_',
                'c-sort-dir' => '_CSORTDIR_',
                'c-page' => 1,
            ], false) . '#' . $hash;
        $commentsModel = [
            "product_id" => $productId,
            "nbComments" => $nbComments,
            "isConnected" => E::userIsConnected(),
            "currentSort" => $sort,
            "uriLogin" => E::link("Ekom_login") . "?hash=$hash",
            "uriSortByDateDesc" => self::getCommentUri($uriPattern, 'date', 'desc'),
            "uriSortByRatingDesc" => self::getCommentUri($uriPattern, 'rating', 'desc'),
            "uriSortByUsefulnessDesc" => self::getCommentUri($uriPattern, 'useful', 'desc'),
            "comments" => $comments,
            "average" => $avg,
        ];


        //--------------------------------------------
        // CREATING THE TAIL
        //--------------------------------------------
        $tail = [
            'features' => $featuresModel,
            'comments' => $commentsModel,
            'featureBar' => [
                'nbComments' => $nbComments,
                'seller' => $head['seller'],
                // ekom composable features, change them if you want to disable some default widgets
                '_features' => true,
            ],
        ];
        Hooks::call("Ekom_ProductPage_decoratePageTailModel", $tail, $head);
        return $tail;
    }


    private static function getCommentUri($uriPattern, $sort, $sortDir)
    {
        return str_replace([
            '_CSORT_',
            '_CSORTDIR_',
        ], [
            $sort,
            $sortDir,
        ], $uriPattern);
    }
}