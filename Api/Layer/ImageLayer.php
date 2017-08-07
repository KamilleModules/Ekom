<?php


namespace Module\Ekom\Api\Layer;


use DirScanner\YorgDirScannerTool;
use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Module\Ekom\Api\Exception\EkomApiException;
use ThumbnailTools\ThumbnailTool;

class ImageLayer
{


    /**
     *
     *
     * This method can be used to fetch any images handled by the Ekom module.
     *
     * All images are jpg (not png or other type).
     *
     *
     * Images should be organized like this:
     *
     * $baseDir = /app/www/modules/ekom/img
     *
     *
     * // images for products
     * - $baseDir/products/thumb
     * - $baseDir/products/small
     * - $baseDir/products/medium
     * - $baseDir/products/large
     *
     *
     * // images for product cards
     * - $baseDir/cards/thumb
     * - $baseDir/cards/small
     * - $baseDir/cards/medium
     * - $baseDir/cards/large
     *
     *
     *
     *
     * @param $type
     * type can be one of:
     *
     * - productBox
     *      the images for a product, combined with the images of the container product card.
     *      It returns a displayCollection (see return forms below).
     *
     * - product
     *      the images for a product.
     *      It returns a displayCollection (see return forms below).
     *
     *
     *
     *
     *
     * @param mixed $id , help finding the images.
     *      Depends on the type.
     *      If type is:
     *          - productBox,
     *                  then the id is an array containing the following:
     *                      - 0: product_id, or array of product_id
     *                      - 1: product_card_id
     *          - product,
     *                  then the id is a string representing the product id
     *
     *
     *
     * Tricks
     * ============
     *
     *
     * Skip images
     * ----------------
     * To prevent an image from being selected by this method, prefix it with an underscore.
     * For instance, _dog.jpg will always be ignored by this system.
     *
     * Default image
     * ----------------
     * The first image which filename contains the string "-default" should be considered as the
     * default image.
     *
     *
     *
     * Return forms
     * ===============
     *
     * - displayCollection
     *
     * displayCollection: is an array of $fileIdentifier => [
     *      thumb => $pathToThumbImage,
     *      small => $pathToSmallImage,
     *      medium => $pathToMediumImage,
     *      large => $pathToLargeImage,
     * ]
     *
     * The $fileIdentifier might be the fileName, or, if the search spans multiple directories,
     * a namespaced fileName (the namespace could be separated from the fileName with a dot).
     *
     *
     *
     * In ekom, all variations of a given image have the same fileName.
     * This allows us to treat all variations as one image, which makes it easier
     * on the upload (the user only uploads one file, and the system takes care of creating
     * the differents variations and placing them in the relevant directories),
     * but also on finding images; for finding images, we take one of the variation and assume that
     * the files for the other variations will exist.
     *
     * This means for instance in a collection, we choose the medium as the default variation.
     * And thus we only need to parse the medium dir, and for each file found in that directory,
     * we will assume that there will also be a corresponding version in the thumb, small and large directories.
     *
     * By the way, medium directory, when available, is always chosen as the base variation.
     *
     *
     * @return array of images uris
     *
     */
    public function getImages($type, $id, $ignoreSkippable = true)
    {

        $ret = [];

        switch ($type) {
            case 'productBox':
                list($productIds, $cardId) = $id;
                if (!is_array($productIds)) {
                    $productIds = [$productIds];
                }

                $this->parseCollectionByIds($ret, $productIds, "products", "product", $ignoreSkippable);
                $this->parseCollectionByIds($ret, [$cardId], "cards", "card", $ignoreSkippable);
                break;
            case 'product':
                $this->parseCollectionByIds($ret, [$id], "products", "product", $ignoreSkippable);
                break;
            default:
                break;
        }
        return $ret;
    }

    /**
     * Like getImages, but removes entry starting with _, and return the default image too.
     *
     * @return array:
     *          - 0: string, defaultImageIdentifier.
     *                      If empty string, means the displayCollection is empty
     *          - 1: array, displayCollection, as defined in getImages
     */
    public function getImagesInfo($type, $id, $ignoreSkippable)
    {
        $defaultImage = "";
        $images = $this->getImages($type, $id, $ignoreSkippable);
        if ($images) {

            $defaultImage = key($images);
            foreach ($images as $identifier => $arr) {
                if (false !== strpos($identifier, '-default')) {
                    $defaultImage = $identifier;
                }
            }
        }
        return [$defaultImage, $images];
    }

    /**
     * @param $path , src to the original image to copy
     * @param $type , string, one of:
     *          - products
     *          - cards
     *
     * @param $params , mixed, depends on the type, if the type is:
     *          - products: then params is the product_id
     *          - cards: then params is the product_card_id
     *
     *
     * @throws EkomApiException
     */
    public function createImageCopy($path, $type, $params)
    {
        $imgDir = $this->getImagesDir();

        switch ($type) {
            case 'products':
                $productId = $params;
                $dst = $imgDir . "/products/" . $this->getHashedPath($productId);
                $this->createImageCollection($path, $dst);
                break;
            case 'cards':
                $cardId = $params;
                $dst = $imgDir . "/cards/" . $this->getHashedPath($cardId);
                $this->createImageCollection($path, $dst);
                break;
            default:
                throw new EkomApiException("Type not found: $type");
                break;
        }
    }


    /**
     * @return string, the path to the physical directory containing all the images
     * handled by the ekom module.
     *
     * It's hardcoded for now(, because I believe the less it can be changed, the less "sync" problems we will have).
     *
     */
    public function getImagesDir()
    {
        return ApplicationParameters::get("app_dir") . "/www/" . self::getImagesUriPrefix();
    }

    public function getImagesUriPrefix()
    {
        return "/modules/Ekom/img";
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    private function getHashedPath($string)
    {
        return implode('/', str_split($string, 1));
    }

    private function createImageCollection($src, $dstDir)
    {
        $types = [
            'thumb' => [80, 60],
            'small' => [200, 150],
            'medium' => [800, 600],
            'large' => [1200, 900],
        ];
        return $this->createImageSerial($src, $dstDir, $types);
    }

    private function createImageSerial($src, $dstDir, array $types)
    {
        $fileName = basename($src);
        foreach ($types as $dir => $dims) {
            list($w, $h) = $dims;
            $path = $dstDir . "/" . $dir . "/" . $fileName;
            ThumbnailTool::biggest($src, $path, $w, $h);
        }
        return true;
    }

    private function parseCollectionByIds(array &$ret, array $ids, $type, $identifierPrefix, $ignoreSkippable = true)
    {
        $imgDir = $this->getImagesDir();
        $baseUri = $this->getImagesUriPrefix();
        foreach ($ids as $itemId) {
            $hash = $this->getHashedPath($itemId);
            $dirPath = $imgDir . "/$type/$hash/medium";
            if (file_exists($dirPath)) {
                $imgs = YorgDirScannerTool::getFilesWithExtension($dirPath, ['jpg', 'jpeg'], false, false, false);
                foreach ($imgs as $img) {
                    $baseName = basename($img);
                    if (true === $ignoreSkippable && 0 === strpos($baseName, '_')) {
                        continue;
                    }
                    $identifier = "$identifierPrefix-$itemId." . $baseName;
                    $ret[$identifier] = [
                        "thumb" => $baseUri . "/$type/$hash/thumb/" . $baseName,
                        "small" => $baseUri . "/$type/$hash/small/" . $baseName,
                        "medium" => $baseUri . "/$type/$hash/medium/" . $baseName,
                        "large" => $baseUri . "/$type/$hash/large/" . $baseName,
                    ];
                }
            }
        }

    }
}