<?php


namespace Module\Ekom\Utils\OrderBuilder\Step;


interface OrderBuilderStepInterface
{

    /**
     * @param $context , the orderBuilder context
     * @param bool $justDone , whether or not the step's form has
     *              just been successfully posted.
     *              In which case the return of this method is void.
     * @return array|void,
     *              - array: the step model
     *              - void: if the step's form has just been successfully
     *                      posted, this method returns nothing
     *
     *
     * @throws \Exception if a data is expected in the context but not found
     *
     */
    public function process($context, &$justDone = false);

    /**
     * @param $context , the orderBuilder context
     * @return bool
     */
    public function isRelevant($context);
}