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

    /**
     * I had the case where step needs to fix the userStep.
     * This happened to me with a step composed of multiple sub-steps,
     * so each sub-step calls the main userStep (circumventing
     * the default OrderBuilder to keep the focus on the sub-steps),
     * but the form submission of the very last sub-step, if successful, should
     * not call the main userStep, but rather let
     * OrderBuilder handles the step the normal way.
     *
     * Whether the last sub-steps' form submission is successful or not can only be
     * decided by the very last sub-step, hence the need for this pre-loop.
     *
     * So for instance I had the training step, which was composed of three parts (each part being
     * a form).
     * To have the focus directed on the training step for every part, I sent the POST.step variable
     * to training on every sub-step.
     * But on the very last step, if the form is successful, I use fixStep to reset step=training
     * (which comes from POST) to null, so that the OrderBuilder can focus the next step (i.e. the step
     * after training)
     *
     *
     *
     */
    public function fixStep(&$step);
}