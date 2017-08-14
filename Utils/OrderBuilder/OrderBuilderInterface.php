<?php


namespace Module\Ekom\Utils\OrderBuilder;


use Module\Ekom\Utils\OrderBuilder\Step\OrderBuilderStepInterface;


/**
 * This object is a helper for creating an order in ekom.
 * See the documentation in order-builder.md (in ekom doc).
 */
interface OrderBuilderInterface
{

    public function getContext();

    public function getOrderModel();

    public function registerStep($id, OrderBuilderStepInterface $step);


    /**
     * Step data is persistent (it's usually stored in session).
     * It helps keeping track of which step we are currently in.
     */
    public function setStepData($step, $data);

    /**
     * @return array,
     *      each step creates two entries in this array:
     *
     *      - idXXX, the id of step xXX
     *      - stateXXX, the state of step xXX
     *
     * Plus, there is an extra property:
     *
     *      - model: array|null, the current step's model,
     *              Or null if there is no current step (all steps completed)
     *
     *
     */
    public function getStepsInfo();

    /**
     * Clean all persistent data pertaining to this OrderBuilder instance
     */
    public function clean();

    /**
     * @return bool, whether or not all (non irrelevant) steps have the "done" state
     */
    public function isCompleted();

}