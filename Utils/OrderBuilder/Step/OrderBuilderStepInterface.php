<?php


namespace Module\Ekom\Utils\OrderBuilder\Step;


interface OrderBuilderStepInterface
{
    public function isDone();


    public function listen();
}