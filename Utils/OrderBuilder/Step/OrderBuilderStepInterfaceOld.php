<?php


namespace Module\Ekom\Utils\OrderBuilder\Step;


use Module\Ekom\Utils\OrderBuilder\OrderBuilderInterface;

interface OrderBuilderStepInterfaceOld
{

    /**
     * @param OrderBuilderInterface $builder
     *              - the step can/should access the context using the builder instance
     *
     * @param bool $justDone , whether or not the step's form has
     *              just been successfully posted.
     *              In which case the return of this method is void.
     *              Also in this case, the step should set session data using the given
     *              OrderBuilder instance.
     *
     * @param array $stepData , the step data, to provide default values for forms (this happens
     *                              when the user goes back/forth between the steps)
     *
     *
     * @return array|void,
     *              - array: the step model
     *              - void: if the step's form has just been successfully
     *                      posted, this method returns nothing
     *
     *
     * @throws \Exception if a data is expected in the context but not found
     *
     */
    /**
     * @param bool $justDone
     * @return mixed
     */
    public function process(OrderBuilderInterface $builder, &$justDone = false, array $stepData = []);

    /**
     * @param $context , the orderBuilder context
     * @return bool
     */
    public function isRelevant($context);

}