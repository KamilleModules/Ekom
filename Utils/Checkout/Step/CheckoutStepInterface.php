<?php


namespace Module\Ekom\Utils\Checkout\Step;



use Kamille\Architecture\Response\ResponseInterface;

interface CheckoutStepInterface{


    /**
     * @param array $stepData,
     *                  if the form induced by the model is successfully posted,
     *                  the step must return an array of data to save.
     *                  Note: if your step don't have meaningful data to return,
     *                  just return an array with an 1 for instance (or any data).
     * @param array $defaults,
     *                  some default value to configure the step
     *
     *
     *
     * @return array|ResponseInterface, the model to return to the view, or a response.
     */
    public function listen(array &$stepData=null, array $defaults=[]);
}