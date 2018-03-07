<?php


require_once __DIR__ . "/../../vendor/autoload.php";

/**
 * https://github.com/nwtn/php-respimg
 *
 *
 * Suggested:
 *
 * - 480 -> 600
 * - 960
 * - 1440
 * - 1800
 * - 3600
 *
 *
 */

ini_set("display_errors", "1");



$input_filename = "/Volumes/Macintosh HD 2/it/php/projects/leaderfit/leaderfit/www/img/test/slide-top.jpg";
$output_width = 3600;
$output_height = null;
$output_filename = "/Volumes/Macintosh HD 2/it/php/projects/leaderfit/leaderfit/www/img/test/slide-top2.jpg";


$image = new nwtn\Respimg($input_filename);
$image->smartResize($output_width, $output_height, false);
$image->writeImage($output_filename);

echo "pou";