<?php


namespace Module\Ekom\Utils;

use Module\Ekom\Models\EkomModels;


/**
 * This is a private helper, used by me and other developers who want to use it.
 * The idea is to centralize the creation of uri which form I'm not sure yet.
 */
class EkomLinkHelper
{


    public static function getUri($type, $param1 = null)
    {
        switch ($type) {
            case 'removeProductFromCart':
                // param1=product_id
                return "/service/Ekom/gscp/api?action=removeProductFromCart&product_id=" . $param1;
                break;
            case 'updateCartProduct':
                // param1=product_id
                return "/service/Ekom/gscp/api?action=updateCartProduct&product_id=" . $param1 . '&qty'; // need to append "=$qty" to it
                break;
            default:
                throw new \Exception("Unknown type: $type");
                break;
        }
    }


    /**
     * This method is meant to be used by modules who want to create the productModifiers list.
     * @see EkomModels::productModifiersListModel()
     *
     * The page_uri property of this model is an uri representing the page.
     * And this method helps creating the page_uri.
     *
     * It is actually recommended that modules rely on this method for consistency,
     * and basically let Ekom decide which form the uri should have, which is either using the product reference id
     * (encapsulating the product details in the product_reference_id), or exposing the product details in the uri via
     * GET parameters.
     *
     * Normally, I already thought about that and Ekom will expose the product details in the uri
     * (as they don't have enough meaning to justify the creation of new uris, basically).
     *
     *
     *
     * @param array $earlyProductBoxModel , the productBoxModel in construction (latest properties not yet defined).
     * @see EkomModels::productBoxModel()
     * @param array $productDetails
     */
    public static function getPreferredProductPageUri(array $earlyProductBoxModel, array $productDetails = [])
    {

        $sSuffix = "";
        if ($productDetails) {
            $sSuffix .= "?" . http_build_query($productDetails);
        }
        return E::link("Ekom_productCardRefId", [
                "slug" => $earlyProductBoxModel['product_card_slug'],
                "refId" => $earlyProductBoxModel['product_reference_id'],
            ]) . $sSuffix;
    }


    public static function getAjaxProductPageUri(array $earlyProductBoxModel, array $productDetails = [])
    {
        $productId = $earlyProductBoxModel['product_id'];
        $sSuffix = "";
        if ($productDetails) {
            $sSuffix .= "&" . http_build_query($productDetails);
        }
        return E::link("Ekom_productCardRefId", [
                "slug" => $earlyProductBoxModel['product_card_slug'],
                "refId" => $earlyProductBoxModel['product_reference_id'],
            ]) . "?action=product.getInfo&id=$productId" . $sSuffix;
    }
}