<?php


namespace Module\Ekom\Api\Layer;


use Module\Ekom\Utils\E;
use ThumbnailTools\ThumbnailTool;


/**
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
    public function createImageCopy(string $imgPath, int $imageId)
    {
        $imgDir = E::getImgBaseUri() . "/cp";

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
            'thumb' => [80, 80],
            'small' => [125, 125],
            'medium' => [250, 250],
            'large' => [800, 800],
            'original' => [1000, 1000],
        ];
        return $this->createImageSerial($src, $dstDir, $types);
    }

    private function createImageSerial($src, $dstDir, array $types)
    {
        $ret = [];
        $fileName = basename($src);
        foreach ($types as $dir => $dims) {
            list($w, $h) = $dims;
            $path = $dstDir . "/" . $dir . "/" . $fileName;
            ThumbnailTool::biggest($src, $path, $w, $h);
            $ret[] = $path;
        }
        return $ret;
    }

}