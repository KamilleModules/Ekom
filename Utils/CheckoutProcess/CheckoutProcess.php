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
 *
 * The main method is execute, which returns the checkoutProcess model (displayed by the view).
 *
 * There are many steps, but only one can be opened at a time.
 *
 * Which step is displayed?
 * - the current step is either the last visited step (if it exists), or the first non skippable step
 * otherwise (see source code for more info).
 *
 * Note:
 * when the user disconnects, navigational data is lost (we start back at step 1) even if she reconnects.
 *
 * If the current step is posted successfully, the model immediately uses the next step if any, or otherwise
 * triggers the onStepsComplete callback.
 *
 *
 * GUI ACTIONS
 * ---------------
 * There are two main gui actions:
 *
 * - click
 * - post
 *
 * click is the action of clicking on a step bar (the bar that contains the step title).
 * A click's goal is to select the current step being displayed in the view.
 * However, the model might disallow a click if a previous step has not yet been validated
 * (i.e. you can only click on already done step).
 *
 *
 * post is when you click the step's form SUBMIT button.
 * The context (ensemble of form variables) is sent to the CheckoutProcess object,
 * and the isPostedSuccessfully method of the current step is then called.
 * When this method is successful (returns true), then the next step is selected (and shown in the view).
 * If there is no more next steps, the onStepsComplete callback is triggered.
 *
 *
 * In the current Ekom implementation, both click and post use ajax services (see ekom's ecp/api.php):
 * - click calls: checkout.switchStep (and the special variable _step is used)
 * - post calls: checkout.updateStep (and the context is passed)
 *
 *
 *
 *
 *
 *
 *
 *
 *
 */
class CheckoutProcess implements CheckoutProcessInterface
{

    use EkomContextAwareTrait;


    private static $inst = null;

    private $presteps;
    /**
     * @var CheckoutProcessStepInterface[]
     * array of name => CheckoutProcessStepInterface
     */
    private $steps;


    private function __construct()
    {
        $this->presteps = [];
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

    public static function getCheckoutData()
    {
        $ret = static::currentCheckoutDataAll();
        unset($ret['CheckoutProcess']);

        if (array_key_exists("billing_synced_with_shipping", $ret)) {
            if (1 === (int)$ret['billing_synced_with_shipping']) {
                if (
                    array_key_exists("shipping_address_id", $ret) &&
                    array_key_exists("billing_address_id", $ret)
                ) {
                    $ret['billing_address_id'] = $ret['shipping_address_id'];
                }
            }
        }

        return $ret;
    }


    //--------------------------------------------
    //
    //--------------------------------------------


    public function reset()
    {
        static::currentCheckoutDataSet("CheckoutProcess", []);
        return $this;
    }

    public function set($key, $value)
    {
        $data = static::currentCheckoutDataGet("CheckoutProcess", []);
        $data[$key] = $value;
        static::currentCheckoutDataSet("CheckoutProcess", $data);
    }

    /**
     * keys prefixed with underscore (_) are reserved for this class internal use
     */
    public function get($key, $default = null, $throwEx = false)
    {
        $data = static::currentCheckoutDataGet("CheckoutProcess", []);
        if (array_key_exists($key, $data) && null !== $data[$key]) {
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

        $this->initSteps(); // put steps in the order defined by position


        $processName = get_called_class();
        $this->debug("$processName:execute");
        if ($this->steps) {

            if (null === $context) {
                $context = array_replace($_GET, $_POST, $_FILES);
            }


            $this->debug("Context: " . ArrayToStringTool::toPhpArray($context));


            // do we really need this?
//            $this->spreadContext($context);

            /**
             * @deprecated
             * (now it's much simpler..., so no docs for now...)
             * What's the current step.
             * The algorithm is described in
             * class-modules/Ekom/doc/checkout/checkout-process.md
             */
            //--------------------------------------------
            // WHAT'S THE CURRENT STEP?
            //--------------------------------------------
            $currentStep = $this->get("_lastVisitedStep", $this->getFirstNonSkippableStepName());
            $clickedStep = (array_key_exists('_step', $context)) ? $context['_step'] : null;
            if (null !== $clickedStep) {
                $this->debug("clickedStep: $clickedStep");
                $currentStep = $clickedStep;

            }

            $this->debug("currentStep: $currentStep");
            if (false !== ($regressionStep = $this->getFirstFailingStepNameUntil($currentStep))) {
                $currentStep = $regressionStep;
            } elseif (null !== $clickedStep) {
                $this->getStep($currentStep)->click();
            }

            //--------------------------------------------
            // HANDLE IF IT'S POSTED
            //--------------------------------------------
            if ($this->getStep($currentStep)->isPostedSuccessfully($this, $context)) {
                $this->debug("Step $currentStep was actually posted successfully.");
                /**
                 * If the step is successfully posted, we automatically go to the very next step
                 */
                $currentStep = $this->getNextStep($currentStep); // null|string
                $this->debug("Moving to next active step: $currentStep");
            }


            //--------------------------------------------
            // SAVE THE lastVisitedStep for the next time
            //--------------------------------------------
            $this->set("_lastVisitedStep", $currentStep);


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

    public function addStep(CheckoutProcessStepInterface $step, $name = null, $position = 0)
    {
        if (null === $name) {
            $name = get_class($step);
        }
        $this->presteps[$position][] = [$name, $step];
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
//        E::dlog($msg);
    }


    //--------------------------------------------
    // CURRENT CHECKOUT DATA
    //--------------------------------------------
    protected static function currentCheckoutDataSet($key, $value)
    {
        CurrentCheckoutData::set($key, $value);
    }

    protected static function currentCheckoutDataGet($key, $default = null)
    {

        return CurrentCheckoutData::get($key, $default);
    }

    protected static function currentCheckoutDataAll()
    {
        return CurrentCheckoutData::all();
    }

    //--------------------------------------------

    //
    //--------------------------------------------
    private function getStepModel($stepName)
    {
        if (null !== $stepName && array_key_exists($stepName, $this->steps)) {
            $this->debug("Call $stepName.getModel");
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
        /**
         * This happened to me when using two CheckoutProcesses in parallel
         * (EkomCheckoutProcess + EkomEstimateCheckoutProcess),
         * and when going to Ekom.payment step,
         * then when switching to the estimate branch, the step is ported,
         * but EkomEstimate.payment step doesn't exit.
         *
         * In this case, a simple workaround (not solution)
         * is to go to the previous step.
         * This is what's implemented below.
         */
        $this->reset();
        $stepName = $this->getVeryFirstStep();
        return $this->steps[$stepName];
//        throw new EkomException("This step doesn't exist: $stepName");


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
//                "isActive" => $step->isActive(),
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


    private function initSteps()
    {
        ksort($this->presteps);
        foreach ($this->presteps as $position => $allSteps) {
            foreach ($allSteps as $stepInfo) {
                list($name, $step) = $stepInfo;
                $this->steps[$name] = $step;
            }
        }
    }


    /**
     * Check all step BEFORE given stepName (excluded),
     * and return either false (if all steps are valid),
     * or the name of the first non valid step.
     */
    private function getFirstFailingStepNameUntil(string $stepName)
    {
        foreach ($this->steps as $name => $step) {
            if ($stepName === $name) {
                return false;
            }
            if (false === $step->isValid()) {
                return $name;
            }
        }
        return false;
    }


    private function getFirstNonSkippableStepName()
    {
        foreach ($this->steps as $name => $step) {
            if (true === $step->isSkipped()) {
                continue;
            }
            return $name;
        }
    }
}