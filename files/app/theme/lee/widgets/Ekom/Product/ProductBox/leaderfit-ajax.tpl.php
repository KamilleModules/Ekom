<?php


use Theme\Lee\Ekom\ProductBox\AjaxEventProductBoxRenderer;
use Theme\Lee\Ekom\ProductBox\AjaxProductBoxRenderer;
use Theme\Lee\Ekom\ProductBox\AjaxTrainingProductBoxRenderer;
use Theme\Lee\Ekom\ProductBox\TrainingProductBoxRenderer;

switch ($v['seller']) {
    /**
     * @todo-ling: change seller formation to lf-formation?
     */
    case 'formation':
        AjaxTrainingProductBoxRenderer::create()->render($v);
        break;
    case 'lf-events':
        AjaxEventProductBoxRenderer::create()->render($v);
        break;
    default:
        AjaxProductBoxRenderer::create()->render($v);
        break;
}
