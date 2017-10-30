<?php


namespace Module\Ekom\Utils\Checkout;


use Core\Services\Hooks;
use Kamille\Architecture\Response\ResponseInterface;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Session\EkomSession;
use Module\Ekom\Utils\Checkout\Step\CheckoutStepInterface;
use Module\Ekom\Utils\Checkout\Step\LoginCheckoutStep;
use Module\Ekom\Utils\Checkout\Step\PaymentCheckoutStep;
use Module\Ekom\Utils\Checkout\Step\ShippingCheckoutStep;


class CheckoutPageUtil
{

    private static $sessionName = "Ekom-checkout-stepManager-2017-10-26";


    protected $useTraining;
    protected $usePayment;
    protected $useEvent;
    /**
     * Allows us to switch between the checkoutLayer and the estimateCheckoutLayer
     * without actually changing the code (when possible) of the json/gscp services.
     *
     * See inside AjaxHandlerLayer, called by Hooks.Ekom_configureCheckoutLayerProvider
     */
    protected $checkoutProvider;


    //--------------------------------------------
    //
    //--------------------------------------------
    private $_stepNames;

    /**
     * @var CheckoutStepInterface[]
     */
    private $steps;


    public function __construct()
    {
//        $this->_cleanSessionVars();
        $this->useTraining = true;
        $this->usePayment = true;
        $this->useEvent = true;
        $this->checkoutProvider = 'ekom';
        $this->steps = []; // registered steps


        // cache
        $this->_stepNames = null;
    }

    public static function create()
    {
        return new static();
    }


    public function registerStep($name, CheckoutStepInterface $step, $position)
    {
        $this->_stepNames[$position] = $name;
        $this->steps[$name] = $step;
        return $this;
    }


    /**
     * @return array|ResponseInterface, the model to display, or a response to return to a controller
     */
    public function getModel()
    {
        EkomSession::set("checkoutProvider", $this->checkoutProvider);


        //--------------------------------------------
        // GET STEP ORDER
        //--------------------------------------------
        /**
         * Those positions (0,1000,2000) are my defaults.
         * If you use this class, you can assume that those numbers won't change.
         */
        $this->registerStep("login", LoginCheckoutStep::create(), 1000);
        $this->registerStep("shipping", ShippingCheckoutStep::create(), 2000);
        $this->registerStep("payment", PaymentCheckoutStep::create(), 3000);

        Hooks::call("Ekom_CheckoutPageUtil_registerSteps", $this);


        // be sure to do this once only, and AFTER steps are registered
        ksort($this->_stepNames);
//        az($this->_stepNames); // be sure the step names are those you want, and in the correct order

        //--------------------------------------------
        // WHICH STEP TO DISPLAY NOW
        //--------------------------------------------
        /**
         * Either the user clicked on a step, or otherwise we use the natural algorithm
         */
        $clickedStep = $this->getClickedStep();
        $currentStep = null;
        if (null === $clickedStep) {
            /**
             * Natural step choosing algorithm
             */
            $states = $this->getStepToDoneStates();
            foreach ($states as $stepName => $isAlreadyDone) {
                if (true === $isAlreadyDone) {
                    // note that if all steps are done, currentStep is still null
                    continue;
                } else {
                    $currentStep = $stepName;
                    break;
                }
            }
        } else {
            /**
             * Clicked step
             */
            throw new \Exception("Not implemented yet");
        }


        $model = null;
        //--------------------------------------------
        // NOW PROCESSING THE CURRENT STEP
        //--------------------------------------------
        if (null !== $currentStep) {

            $stepObject = $this->getStepObjectByName($currentStep);
            $stepData = null;
            $defaults = $this->_getStepData($currentStep);
            $model = $stepObject->listen($stepData, $defaults);


            if (null === $stepData) {
                /**
                 * means the step is not completed.
                 * The model will be returned to the view.
                 */

            } else {
                //--------------------------------------------
                // SUCCESSFUL STEP GOES TO NEXT STEP
                //--------------------------------------------
                /**
                 * means the step was successfully completed,
                 * we save the step data to acknowledge that the step was completed.
                 *
                 * we also go to the next step,
                 * which means we need to ask the next step object for the new model.
                 */
                // saving current data
                $this->saveStepData($currentStep, $stepData);

                // find next step
                $currentStep = self::getNextStep($currentStep, $this->_stepNames);
                if (null !== $currentStep) {
                    $stepObject = $this->getStepObjectByName($currentStep);
                    $stepData = null;
                    $defaults = $this->_getStepData($currentStep);
                    $model = $stepObject->listen($stepData, $defaults);
                } else {
                    /**
                     * Code shouldn't go there, because the last step should redirect the user elsewhere.
                     * Or, if not, update this code block...
                     */
                    return null;
                }
            }


        } else {
            /**
             * Do we come from natural step algorithm or from clicked step?
             */
            throw new \Exception("Not implemented now");
        }

        return $this->getReturnByModel($model, $currentStep); // the model could be a ResponseInterface

    }

    //--------------------------------------------
    //
    //--------------------------------------------
    public static function cleanSessionVars()
    {
        EkomSession::remove(self::$sessionName);
//        EventStep::cleanSessionVars();
    }

    public static function getSessionVars()
    {
        $ret = EkomSession::get(self::$sessionName, null);
        if (null === $ret) {
            $ret = [
                /**
                 * array of stepName => isDone,
                 */
                'done' => [],
                /**
                 * array of stepName => stepDoneData
                 */
                'steps' => [],
//                'active' => null,
            ];
        }
        return $ret;
    }
    /**
     * @return array of completed stepNames => data
     * Note: if a step hasn't been completed, it does not appear in the returned array.
     */
    public static function getStepsData()
    {
        return self::getSessionVars()['steps']; // php 5.4+
    }


    /**
     * @param $stepName
     * @return mixed|false,
     *          false is returned when the key is not found (i.e. the stepName was not completed yet).
     *          Otherwise, return the data collected when the step $stepName was completed.
     */
    public static function getStepData($stepName)
    {
        $stepDatas = self::getStepsData();
        if (array_key_exists($stepName, $stepDatas)) {
            return $stepDatas[$stepName];
        }
        return false;
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    protected function getCartLayer()
    {
        return EkomApi::inst()->cartLayer();
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    /**
     * @return string|null,
     *          return the next step.
     *          If null, it can mean one of two things:
     *              - there is no step at all
     *              - there is no next step
     */
    private static function getNextStep($stepName, array $steps)
    {
        $returnNext = false;
        foreach ($steps as $step) {
            if (false === $returnNext) {
                if ($stepName === $step) {
                    $returnNext = true;
                }
            } else {
                return $step;
            }
        }
        return null;
    }


    private function addNavigationToModel(array &$model, $step, array $steps)
    {
        $model['nameCurrent'] = 'current';
        $model['namePrev'] = 'prev';
        $model['nameNext'] = 'next';

        $prev = null;
        $next = null;

        if (false !== ($index = array_search($step, $steps))) {
            if (array_key_exists($index - 1, $steps)) {
                $prev = $steps[$index - 1];
            }
            if (array_key_exists($index + 1, $steps)) {
                $next = $steps[$index + 1];
            }
        }
        $model['valueCurrent'] = $step;
        $model['valuePrev'] = $prev;
        $model['valueNext'] = $next;
    }






    //--------------------------------------------
    //
    //--------------------------------------------
    private function getClickedStep()
    {
        return null;
    }

    private function getStepToDoneStates()
    {
        $ret = [];
        $sessionVars = self::getSessionVars();
        $dones = $sessionVars['done'];
        foreach ($this->_stepNames as $stepName) {
            $ret[$stepName] = (array_key_exists($stepName, $dones) && true === $dones[$stepName]);
        }
        return $ret;
    }


    /**
     * @param $name
     * @return CheckoutStepInterface
     */
    private function getStepObjectByName($name)
    {
        return $this->steps[$name];
    }


    private function _getStepData($stepName)
    {
        $sessionVars = self::getSessionVars();
        $data = $sessionVars['steps'];
        if (array_key_exists($stepName, $data)) {
            return $data[$stepName];
        }
        return [];
    }

    private function saveStepData($stepName, array $data)
    {
        $sessionVars = self::getSessionVars();
        $sessionVars['steps'][$stepName] = $data;
        $sessionVars['done'][$stepName] = true;
        $this->_setSessionVars($sessionVars);
        return $this;
    }

    private function getReturnByModel($mixed, $currentStep)
    {
        if ($mixed instanceof ResponseInterface) {
            return $mixed;
        }
        $cartModel = $this->getCartLayer()->getCartModel();

        $steps = array_keys($this->steps);
        $step2DoneStates = $this->getStepToDoneStates();
        $barClickStep = null;
        $sessVars = self::getSessionVars();

        $stepsInfo = [
            'model' => $mixed,
            'step' => $currentStep,
            'barClickStep' => $barClickStep,
            'sessVars' => $sessVars, // debug tool, when set
        ];

        /**
         * Feeding stepsInfo with dynamic data
         */
        foreach ($steps as $stepName) {
            if ($currentStep === $stepName) {
                $state = 'active';
            } else {
                $state = (true === $step2DoneStates[$stepName]) ? 'done' : "inactive";
            }
            $uc = ucfirst($stepName);
            $stepsInfo["id" . $uc] = $stepName;
            $stepsInfo["state" . $uc] = $state;
        }
        ksort($stepsInfo);


        $conf = [
            "cartModel" => $cartModel,
            "stepsInfo" => $stepsInfo,
        ];
        return $conf;
    }

    //--------------------------------------------
    // 
    //--------------------------------------------
    private function _setSessionVars(array $vars)
    {
        EkomSession::set(self::$sessionName, $vars);
        return $this;
    }



    /**
     * Keep this in case you want to debug and quickly remove all sessions variables
     * pertaining to this class.
     */
    private function _cleanSessionVars()
    {
        EkomSession::remove(self::$sessionName);
        return $this;
    }

}