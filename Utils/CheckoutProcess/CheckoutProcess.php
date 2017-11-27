<?php


namespace Module\Ekom\Utils\CheckoutProcess;


use ArrayToString\ArrayToStringTool;
use Kamille\Services\XLog;
use Module\Ekom\Exception\EkomException;
use Module\Ekom\Utils\Checkout\CurrentCheckoutData;
use Module\Ekom\Utils\CheckoutProcess\Step\CheckoutProcessStepInterface;
use Module\Ekom\Utils\E;
use Module\Ekom\Utils\Traits\EkomContextAwareTrait;


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
 *
 */
class CheckoutProcess implements CheckoutProcessInterface
{

    use EkomContextAwareTrait;


    private static $inst = null;

    /**
     * @var CheckoutProcessStepInterface[]
     * array of name => CheckoutProcessStepInterface
     */
    private $steps;


    private function __construct()
    {
        $this->steps = [];
        $this->init();
    }

    public static function inst()
    {
        if (null === self::$inst) {
            self::$inst = new static();
        }
        return self::$inst;
    }



    //--------------------------------------------
    //
    //--------------------------------------------

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


    public function reset()
    {
        CurrentCheckoutData::set("CheckoutProcess", []);
        return $this;
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

        $shopId = E::getShopId($this->shopId);
        $langId = E::getLangId($this->langId);
        $currencyId = E::getCurrencyId($this->currencyId);

        $this->debug("CheckoutProcess:execute");
        if ($this->steps) {

            if (null === $context) {
                $context = array_replace($_GET, $_POST, $_FILES);
            }

            $context['shop_id'] = $shopId;
            $context['lang_id'] = $langId;
            $context['currency_id'] = $currencyId;

            $this->debug("Context: " . ArrayToStringTool::toPhpArray($context));

            /**
             * What's the current step.
             * The algorithm is described in
             * class-modules/Ekom/doc/checkout/checkout-process.md
             */
            $currentStep = null;
            $clickedStep = (array_key_exists('_step', $context)) ? $context['_step'] : null;
            $this->debug("clickedStep: $clickedStep");

            //--------------------------------------------
            // DEFINE THE CURRENT STEP
            //--------------------------------------------
            /**
             * Note that the current step might be actually posted, but we handle that in the next block
             */
            /**
             * Clicked step?
             */
            $couldBePosted = true;
            if (null !== $clickedStep && true === $this->stepWasReached($clickedStep)) {
                $currentStep = $clickedStep;
                $couldBePosted = false;
                $this->debug("currentStep becomes clicked step $currentStep");
            } else {
                /**
                 * The default current step is either the lastVisitedStep (if it exists),
                 * or the very first step otherwise.
                 */
                $lastVisitedStep = $this->get("_lastVisitedStep");
                $this->debug("lastVisitedStep: $lastVisitedStep");
                if (null !== $lastVisitedStep) {
                    $currentStep = $lastVisitedStep;
                    $this->debug("currentStep becomes last visited step $currentStep");


                } else {
                    $currentStep = $this->getVeryFirstStep();
                    $this->debug("currentStep becomes the very first step $currentStep");
                }
            }

            //--------------------------------------------
            // POTENTIAL DOWNGRADE
            //--------------------------------------------
            /**
             * Whatever step we've chosen above, if there is a non valid step BEFORE,
             * we want to display that non valid step.
             */
            if (null !== ($previousFailingStep = $this->findPreviousFailingStep($currentStep))) {
                $currentStep = $previousFailingStep;
                $this->debug("regression detected: a previous step failed");
                $this->debug("currentStep becomes $currentStep");
            }


            //--------------------------------------------
            // HANDLE IF IT'S POSTED
            //--------------------------------------------
            if (true === $couldBePosted && $this->getStep($currentStep)->isPostedSuccessfully($this, $context)) {
                $this->debug("Step $currentStep was actually posted successfully, moving to next step...");
                /**
                 * If the step is successfully posted, we automatically go to the very next step
                 */
                $currentStep = $this->getNextStep($currentStep); // null|string
                $this->debug("...$currentStep");
            }


            //--------------------------------------------
            // SAVE THE lastVisitedStep for the next time
            //--------------------------------------------
            $this->set("_lastVisitedStep", $currentStep);


            //--------------------------------------------
            // SAVING THAT THIS POSITION WAS REACHED
            //--------------------------------------------
            $this->markAsReached($currentStep);


            //--------------------------------------------
            // END CALLBACK?
            //--------------------------------------------
            if (null === $currentStep && null !== $onStepsComplete) { // no next step? it's over
                $this->debug("process completed, executing onStepsComplete callback");
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

    protected function debug($msg)
    {
        XLog::log($msg, "debug.log");
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


    private function getVeryFirstStep()
    {
        foreach ($this->steps as $stepName => $step) {
            return $stepName;
        }
        throw new EkomException("There is no very first step, have you set at least ONE step?");
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


    private function markAsReached($stepName)
    {
        /**
         * Note: in development, I had the case where steps BEFORE the current step were not reached,
         * and it bothered me for testing.
         * Although this situation might not occur in prod, I prefer to mark not only the currentStep,
         * but also all previous steps as reached, as to workaround my little annoyance, and
         * preventing the problem to occur again anyway.
         */
        $reached = $this->get("reached", []);
        foreach ($this->steps as $name => $step) {
            $reached[$name] = true;
            if ($stepName === $name) {
                break;
            }
        }
        $this->set("reached", $reached);
    }

    private function stepWasReached($stepName)
    {
        $reached = $this->get("reached", []);
        return array_key_exists($stepName, $reached);
    }

    private function findPreviousFailingStep($currentStep)
    {
        foreach ($this->steps as $stepName => $step) {
            if ($stepName === $currentStep) {
                break;
            }
            if (false === $step->isValid()) {
                return $stepName;
            }
        }
        return null;
    }

}