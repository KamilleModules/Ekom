<?php


namespace Module\Ekom\Utils\CheckoutProcess;


use Module\Ekom\Exception\EkomException;
use Module\Ekom\Utils\Checkout\CurrentCheckoutData;
use Module\Ekom\Utils\CheckoutProcess\Step\CheckoutProcessStepInterface;


/**
 * checkoutProcessModel
 * -------------------------
 * - steps: an array of stepItem models, each of which having the following structure:
 *      name: string, the name of the step
 *      isDone: bool
 *      isCurrent: bool
 *      model: null|array,
 *                  null when the step doesn't have the focus,
 *                  array if the step has focus
 *
 *
 * @deprecated works, but the flow is not optimal (loss of consistency after page refresh)
 *
 */
class CheckoutProcessOld2 implements CheckoutProcessInterface
{

    private static $inst = null;

    /**
     * @var CheckoutProcessStepInterface[]
     * array of name => CheckoutProcessStepInterface
     */
    private $steps;
    /**
     * The first failing step might become the current step (depending on your preferences)
     */
    private $firstFailingStepName;
    private $isCompleteWasCalled;

    private function __construct()
    {
        $this->steps = [];
        $this->firstFailingStepName = null;
        $this->isCompleteWasCalled = false;
        $this->init();
    }

    public static function inst()
    {
        if (null === self::$inst) {
            self::$inst = new static();
        }
        return self::$inst;
    }

    public function setShippingAddressId($id)
    {
        // TODO: Implement setShippingAddressId() method.
    }

    public function setBillingAddressId($id)
    {
        // TODO: Implement setBillingAddressId() method.
    }

    public function setPaymentMethodId($id)
    {
        // TODO: Implement setPaymentMethodId() method.
    }

    public function setCarrierId($id)
    {
        // TODO: Implement setCarrierId() method.
    }

    public function set($key, $value)
    {
        $data = CurrentCheckoutData::get("CheckoutProcess", []);
        $data[$key] = $value;
        CurrentCheckoutData::set("CheckoutProcess", $data);
    }

    /**
     * keys prefixed with underscore (_) are reserved for this class internal use
     */
    public function get($key, $default = null, $throwEx = false)
    {
        $data = CurrentCheckoutData::get("CheckoutProcess", []);
        if (array_key_exists($key, $data)) {
            return $data[$key];
        }
        if (true === $throwEx) {
            throw new EkomException("Key not found in CheckoutProcess session store: $key");
        }
        return $default;
    }


    /**
     * @param callable|null $onStepsComplete
     * @return array, the checkoutProcessModel array as defined at the top of this classx
     * @throws EkomException
     */
    public function execute(callable $onStepsComplete = null, array $context = null)
    {

        if ($this->steps) {

            if (null === $context) {
                $context = array_replace($_GET, $_POST, $_FILES);
            }

            $this->isCompleteWasCalled = true;
            $this->firstFailingStepName = null;


            $clickedStep = (array_key_exists('_step', $context)) ? $context['_step'] : null;

            $firstNonValidStep = $this->getFirstNonValidStep();

            /**
             * If $currentStep is still null at the end of this block below,
             * this means that all steps have been successfully completed.
             */
            $currentStep = null;


            if (null !== $firstNonValidStep) {


                if (null !== $clickedStep && $this->getStep($clickedStep)->isValid()) {
                    /**
                     * If the user clicked on a step he/she already validated, we will display this step
                     */
                    $currentStep = $clickedStep;
                } else {
                    /**
                     * If the step was not clicked, we use the default algorithm (the first non valid step)
                     */
                    $currentStep = $firstNonValidStep;

                    /**
                     * If that step is being posted just now and validates though,
                     * we will display the next step.
                     */
                    if ($this->getStep($currentStep)->isPostedSuccessfully($this, $context)) {
                        $currentStep = $this->getNextStep($currentStep);
                    }
                }

            }


            if (null === $currentStep && null !== $onStepsComplete) {
                call_user_func($onStepsComplete);
            }


            //--------------------------------------------
            // RETURN THE MODEL
            //--------------------------------------------
            $model = [
                'steps' => $this->getModelByCurrentStep($currentStep),
            ];
            $this->decorateModel($model);
            return $model;
        }
        throw new EkomException("Please set at least one step to get started");
    }

    public function addStep(CheckoutProcessStepInterface $step, $name = null)
    {
        if (null === $name) {
            $name = get_class($step);
        }
        $this->steps[$name] = $step;
        return $this;
    }

    public function placeOrder(callable $onSuccess = null)
    {
        if (is_callable($onSuccess)) {
            call_user_func($onSuccess);
        }
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    protected function init()
    {

    }

    protected function decorateModel(array &$model)
    {

    }

    //--------------------------------------------

    //
    //--------------------------------------------
    private function getStepModel($stepName)
    {
        if (null !== $stepName && array_key_exists($stepName, $this->steps)) {
            return $this->steps[$stepName]->getModel();
        }
        return [];
    }

    private function injectCurrentStepInModel($stepName, array &$ret)
    {
        if (array_key_exists($stepName, $ret)) {
            $model = $this->getStepModel($stepName);
            $ret[$stepName]['isCurrent'] = true;
            $ret[$stepName]['model'] = $model;
        }
    }

    private function getFirstNonValidStep()
    {
        foreach ($this->steps as $stepName => $step) {
            if (false === $step->isValid()) {
                return $stepName;
            }
        }
        return null;
    }


    private function getNextStep($stepName)
    {
        $returnNext = false;
        foreach ($this->steps as $_stepName => $step) {
            if (false === $returnNext) {
                if ($stepName === $_stepName) {
                    $returnNext = true;
                }
            } else {
                return $_stepName;
            }
        }
        return null;
    }


    /**
     * @return CheckoutProcessStepInterface
     * @throws EkomException
     */
    private function getStep($stepName)
    {
        if (array_key_exists($stepName, $this->steps)) {
            return $this->steps[$stepName];
        }
        throw new EkomException("This step doesn't exist: $stepName");
    }

    private function getModelByCurrentStep($currentStep)
    {
        $ret = [];
        $firstNonDone = null;
        /**
         * Computing the model dynamically.
         * Note: in this first loop, we set all isCurrent to false and model to null.
         * Next, we will define those properties, but only for the relevant step.
         */
        foreach ($this->steps as $name => $step) {
            $ret[$name] = [
                "name" => $name,
                "isDone" => $step->isValid(),
                "isCurrent" => false,
                "model" => null,
            ];
        }
        // now injecting the current step and the step model to the stepsModel before returning it
        if (null !== $currentStep) {
            $this->injectCurrentStepInModel($currentStep, $ret);
        }
        return $ret;
    }
}