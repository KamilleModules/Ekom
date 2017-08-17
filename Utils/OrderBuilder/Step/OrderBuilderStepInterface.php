<?php


namespace Module\Ekom\Utils\OrderBuilder\Step;


use Module\Ekom\Utils\OrderBuilder\OrderBuilderInterface;

interface OrderBuilderStepInterface
{
    /**
     * @return bool, whether or not the step is considered done
     */
//    public function isDone(OrderBuilderInterface $builder);


    /**
     * @return mixed, depends on isDone
     *          - if isDone is false, return an array (the form model)
     *          - if isDone is true, doesn't need to return anything
     */
    public function listen(OrderBuilderInterface $builder, &$isDone = false);

    /**
     * Clean the step data so that the step becomes "not done".
     */
    public function clean();

    /**
     * @return bool, whether or not the step is relevant to the order.
     * For instance, a poll step might appear only if the cart contains certain type of product.
     */
    public function isRelevant();
}