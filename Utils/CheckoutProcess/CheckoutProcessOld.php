<?php


namespace Module\Ekom\Utils\CheckoutProcess;


use Module\Ekom\Exception\EkomException;
use Module\Ekom\Utils\Checkout\CurrentCheckoutData;
use Module\Ekom\Utils\CheckoutProcess\Step\CheckoutProcessStepInterface;


class CheckoutProcessOld implements CheckoutProcessInterface
{

    private static $inst = null;

    /**
     * @var CheckoutProcessStepInterface[]
     * array of name => CheckoutProcessStepInterface
     */
    private $steps;
    private $context;
    /**
     * The first failing step might become the current step (depending on your preferences)
     */
    private $firstFailingStepName;
    private $isCompleteWasCalled;

    private function __construct()
    {
        $this->steps = [];
        $this->context = [];
        $this->firstFailingStepName = null;
        $this->isCompleteWasCalled = false;
    }

    public static function inst()
    {
        if (null === self::$inst) {
            self::$inst = new static();
        }
        return self::$inst;
    }

    public function setContext(array $context)
    {
        $this->context = $context;
        return $this;
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


    public function isComplete()
    {
        $this->isCompleteWasCalled = true;
        $this->firstFailingStepName = null;


        $context = $this->context;
        /**
         * Note: we will also use the loop to collect stepsData
         */
        foreach ($this->steps as $name => $step) {
            $step->prepare($this, $context);
            if (false === $step->isValid()) {
                if (null === $this->firstFailingStepName) {
                    $this->firstFailingStepName = $name;
                    if (null === $this->firstFailingStepName) {
                        throw new EkomException("The model can't be null");
                    }
                }
            } else {
                $this->setIsDone($name);
            }
        }
        return (null === $this->firstFailingStepName);
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

    public function getStepsModel()
    {
        /**
         * In here we define which step has the focus (amongst other things).
         *
         * By default, we set it to the first non done step,
         * but you might want otherwise.
         *
         */


        $ret = [];
        if (false === $this->isCompleteWasCalled) {
            $this->isComplete();
        }
        $dones = $this->get("_dones", []);
        $firstNonDone = null;
        /**
         * Computing the model dynamically.
         * Note: in this first loop, we set all isCurrent to false and model to null
         */
        foreach ($this->steps as $name => $step) {
            $isDone = (array_key_exists($name, $dones) && true === $dones[$name]);

            if (null === $firstNonDone && false === $isDone) {
                $firstNonDone = $name;
            }

            $ret[$name] = [
                "name" => $name,
                "isDone" => $isDone,
                "isCurrent" => false,
                "model" => null,
            ];
        }


        $currentStep = $firstNonDone;
        if (array_key_exists("_step", $this->context)) {
            $currentStep = $this->context["_step"];
        }


        // now injecting the current step and the step model to the stepsModel before returning it
        if (null !== $currentStep) {
            $this->injectCurrentStepInModel($currentStep, $ret);
        }

        return $ret;
    }

    //--------------------------------------------

    //
    //--------------------------------------------
    private function setIsDone($stepName, $isDone = true)
    {
        $dones = $this->get("_dones", []);
        $dones[$stepName] = $isDone;
        $this->set("_dones", $dones);
        return $this;
    }

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
}