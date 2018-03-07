<?php


use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Theme\Lee\Ekom\Carousel\CarouselProductsRenderer;

$prefix = "/theme/" . ApplicationParameters::get("theme");


CarouselProductsRenderer::create()->render($v);






