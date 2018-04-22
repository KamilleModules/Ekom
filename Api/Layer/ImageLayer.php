<?php


namespace Module\Ekom\Api\Layer;


use Bat\FileSystemTool;
use Core\Services\A;
use Kamille\Services\XLog;
use Module\Ekom\Utils\E;
use ThumbnailTools\ThumbnailTool;


/**
 *
 * Technique of posting a form with ajax images
 * ================================
 *
 * Here is the current technique we use for posting forms with ajax images:
 *
 *
 *
 * FORM IN INSERT MODE
 * -----------------
 * - since the image upload is asynchronous and is executed BEFORE WE HAVE the id of the record,
 * we do the following:
 *      - first we have a separated image table in the database, so that we can add label, legend (seo related things) to the images
 *      - in the front, to display an image, we just need the image id (our system is based on a naming convention).
 *              Basically, each directory is named after the image id and contains the same structure:
 *              example:
 *                  - /img/1/5/4/
 *                          - 154.jpg  (the original: 1000x1000)
 *                          - 154-large.jpg  (800x800)
 *                          - 154-medium.jpg  (250x250)
 *                          - 154-small.jpg  (125x125)
 *                          - 154-thumb.jpg  (80x80)
 *
 *      - we use an "ajax upload widget" which works with an uri input field (that it updates and read from)
 *
 *      - so now when we are on the form to upload a (product/card) image, we know the card_id, the product_id (if the image
 *          is attached to a specific product), but image_id is not available yet (we need to wait for the user to post the form).
 *
 *      - when the user uploads an image, we put the files in a temporary directory, and return the url,
 *      so that the gui can update the ajax widget's url input field and display the relevant image.
 *
 *      - but when the form is posted (then the image id is available), then we parse the url:
 *          - if we detect that it comes from the temporary directory, then we move all temporary files to their new location (based on image_id)
 *          - note that we don't have an url field in the image table, because we rely on the naming convention to display the image.
 *              In other words, the uri is just used by the ajax upload image widget.
 *              And so after the files are moved, we don't need the uri anymore...
 *
 *
 * So in other words, the uri of the image transits via the form, but is just a medium and is
 * never committed to the database.
 *
 *
 *
 * FORM IN UPDATE MODE
 * -----------------
 * - we do have the image id, so we can just upload the files directly to their right location, avoiding complications.
 *
 *
 *
 *
 *
 *
 */
class ImageLayer
{

    /**
     *
     * Image types:
     * ------------
     *      - large: 800x800
     *      - medium: 250x250
     *      - small: 125x125
     *      - thumb: 80x80
     *      - original: 1000x1000
     */
    public static function createTmpImageCardCollection(string $imgPath, string $dstDir = null)
    {
        $ret = [];
        if (null === $dstDir) {
            $dstDir = A::appDir() . "/www" . E::getImgBaseUri() . "/tmp/" . date("YmdHis") . mt_rand();
        }

        $types = self::getTypes();
        $extension = FileSystemTool::getFileExtension($imgPath);

        foreach ($types as $type => $dims) {
            list($w, $h) = $dims;
            $path = $dstDir . "/$type.$extension";
            ThumbnailTool::biggest($imgPath, $path, $w, $h);
            $ret[] = $path;
        }
        return $ret;
    }


    public static function convertUriType(string $uri, string $type)
    {
        $baseName = basename($uri);
        $p = explode('-', $baseName);
        $imageId = $p[0];
        $dir = dirname($uri);
        $extension = FileSystemTool::getFileExtension($baseName);
        if ('original' === $type) {
            return $dir . "/$imageId.$extension";
        }
        return $dir . "/$imageId-$type.$extension";
    }


    public static function createRealImageCardCollection(string $imgPath, int $imageId)
    {
        $ret = [];
        $dstDir = A::appDir() . "/www" . E::getImgBaseUri() . "/cp";
        $types = self::getTypes();
        $extension = FileSystemTool::getFileExtension($imgPath);

        foreach ($types as $type => $dims) {
            list($w, $h) = $dims;
            $hash = self::getHashedPath($imageId);
            $path = $dstDir . "/$hash/$imageId-$type.$extension";
            ThumbnailTool::biggest($imgPath, $path, $w, $h);
            $ret[] = $path;
        }
        return $ret;
    }

    public static function moveTmpImagesToTheirRealLocation(string $mediumImgUri, int $imageId)
    {
        // we will only process uri if they are in the tmp directory
        if (0 === strpos($mediumImgUri, E::getImgBaseUri() . "/tmp/")) {
            $tmpDir = A::appDir() . "/www" . dirname($mediumImgUri);
            $extension = FileSystemTool::getFileExtension($mediumImgUri);
            $dstDir = self::getCardProductImageDirByImageId($imageId);

            $map = [
                "thumb" => $imageId . "-thumb",
                "small" => $imageId . "-small",
                "medium" => $imageId . "-medium",
                "large" => $imageId . "-large",
                "original" => $imageId,
            ];

            foreach ($map as $srcName => $dstName) {
                $srcFile = $tmpDir . "/$srcName.$extension";
                $dstFile = $dstDir . "/$dstName.$extension";
                FileSystemTool::rename($srcFile, $dstFile);
            }

        }
    }


    public static function getCardProductImageDirByImageId(int $imageId, $returnUri = false)
    {
        $hash = implode("/", str_split((string)$imageId));
        $uri = E::getImgBaseUri() . "/cp/$hash";
        if (true === $returnUri) {
            return $uri;
        }
        return A::appDir() . "/www" . $uri;
    }


    public static function getCardProductImageUriByImageId(int $imageId, $imgType = null)
    {
        if (null === $imgType) {
            $imgType = "medium";
        }
        $uriBase = self::getCardProductImageDirByImageId($imageId, true) . "/$imageId-$imgType.";
        $extensions = [
            "jpg",
            "jpeg",
            "png",
        ];
        $imgDir = A::appDir() . "/www";
        foreach ($extensions as $extension) {
            if (file_exists($imgDir . $uriBase . $extension)) {
                return $uriBase . $extension;
            }
        }
        XLog::error("[Ekom.ImageLayer] -- image with id $imageId not found in the file system: $uriBase.$extension");
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    private static function getTypes()
    {
        return [
            'thumb' => [80, 80],
            'small' => [125, 125],
            'medium' => [250, 250],
            'large' => [800, 800],
            'original' => [1000, 1000],
        ];
    }

    private static function getHashedPath(string $string)
    {
        return implode('/', str_split($string, 1));
    }
}