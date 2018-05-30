<?php


namespace Module\Ekom\Api\Util;


use Bat\UriTool;
use Module\Ekom\Utils\E;

class UriUtil
{


    public static function getProductBoxBaseAjaxUri($productId)
    {
        return E::link("Ekom_ajaxApi") . "?action=getProductInfo&id=" . $productId;
    }

    public static function getProductBoxUriByCardSlugProductRef($cardSlug, $productRef, array $productDetails = [])
    {
        $uri = E::link("Ekom_productCardRef", ['slug' => $cardSlug, 'ref' => $productRef]);
        if ($productDetails) {
            $uri = UriTool::uri($uri, $productDetails, true);
        }
        return $uri;
    }

    public static function getProductBoxUriByCardSlugProductRefId($cardSlug, int $productRefId, array $productDetails = [])
    {
        $uri = E::link("Ekom_productCardRefId", ['slug' => $cardSlug, 'refId' => $productRefId]);
        if ($productDetails) {
            $uri = UriTool::uri($uri, $productDetails, true);
        }
        return $uri;
    }
}