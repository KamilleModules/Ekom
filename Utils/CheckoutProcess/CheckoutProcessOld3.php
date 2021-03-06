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
 *
 *
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
class CheckoutProcessOld3 implements CheckoutProcessInterface
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

        $this->initSteps(); // put steps in the order defined by position

        $this->debug("CheckoutProcess:execute");
        if ($this->steps) {

            if (null === $context) {
                $context = array_replace($_GET, $_POST, $_FILES);
            }


            $this->debug("Context: " . ArrayToStringTool::toPhpArray($context));

            $this->spreadContext($context);

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
                $this->debug("regression detected: a previous step failed: $previousFailingStep");
                $this->debug("currentStep becomes $currentStep");
            }


            //--------------------------------------------
            // HANDLE IF IT'S POSTED
            //--------------------------------------------
            if (true === $couldBePosted) {
                $this->debug("Is $currentStep posted successfully?");
                if ($this->getStep($currentStep)->isPostedSuccessfully($this, $context)) {
                    $this->debug("Step $currentStep was actually posted successfully.");
                    /**
                     * If the step is successfully posted, we automatically go to the very next step
                     */
                    $currentStep = $this->getNextStep($currentStep); // null|string
                    $this->debug("Moving to next active step: $currentStep");
                }
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
        E::dlog($msg);
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
        $this->debug("start findPreviousFailingStep");
        foreach ($this->steps as $stepName => $step) {
            $this->debug("loop: $stepName");
            if ($stepName === $currentStep) {
                break;
            }
            if (false === $step->isValid()) {
                return $stepName;
            }
        }
        return null;
    }

    private function spreadContext(array $context)
    {
        foreach ($this->steps as $step) {
            $step->prepare($context);
        }
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
}