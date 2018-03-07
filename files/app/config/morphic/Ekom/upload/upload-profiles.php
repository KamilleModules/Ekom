<?php


use Kamille\Architecture\ApplicationParameters\ApplicationParameters;

$appDir = ApplicationParameters::get("app_dir");
$baseDir = $appDir . '/www/uploads/Ekom';


/**
 * @todo-ling: safe uploader pattern... SafeUploader pattern
 */
$conf = [
    'ek_seller.image' => [
        /**
         * The dir in which the uploaded file should be put.
         * You can use the inserted data (including auto-incremented key) as part of the dir path,
         * just wrap the column name with curly brackets (for instance {id}).
         *
         */
        'dir' => $baseDir . '/ek_seller/{id}',
        /**
         * Thumbs only applies if isImage is true.
         * It allows you to make copies of the original uploaded image.
         * The thumbs are usually smaller than the original, but they could be greater too, depending
         * on the image library installed on your system.
         *
         * Each item of thumbs is a thumbItem, which has the following structure:
         *      - ?maxWidth, if set this will be the maximum width of the image
         *      - ?maxHeight, if set this will be the maximum height of the image
         *      - ?preserveRatio=true, boolean. If set to false AND both maxWitdth and maxHeight are defined, then
         *                          maxWidth and maxHeight will actually be the exact width and height of the thumb,
         *                          it might distort the image.
         *      - ...more to come later probably (a naming function would be welcome...)
         *
         */
        'thumbs' => [
            [
                "maxWidth" => 300,
            ],
        ],
        /**
         * If set and true, will apply special security restrictions to the uploaded file
         */
        'isImage' => true,
        /**
         * The maximum size of the uploaded file.
         * The following filesize units can be used (case does NOT matter):
         *
         * - b: bytes
         * - o: octet, alias for bytes
         * - k: kilo bytes
         * - kb: alias for kilo bytes
         * - ko: alias for kilo bytes
         * - m: mega bytes
         * - mb: alias for mega bytes
         * - mo: alias for mega bytes
         *
         *
         */
        'maxSize' => '2M',
    ],
];