<?php


namespace Module\Ekom\Utils\Checkout\Step;


/**
 * Synopsis
 * -----------
 *
 *
 *      prepare
 *
 *      if isSuccessfullyPosted
 *          then getStepData
 *      else
 *          then getFormModel
 *      endif
 *
 *
 *
 */
interface CheckoutStepInterface
{


    /**
     * @return string, a label representing this step
     */
    public function getLabel();


    /**
     * Initialize the step
     *
     * @param $stepData, see definition in CheckoutPageUtil
     * @param $context, see definition in CheckoutPageUtil
     */
    public function prepare(array $stepData, array $context);

    /**
     * @return bool, whether or not the formData posted by the user is valid
     *
     */
    public function isSuccessfullyPosted();

    public function getStepData();


    /**
     * Return the model of the form for this step.
     *
     *
     * @return array, the step's form model
     */
    public function getFormModel();


}