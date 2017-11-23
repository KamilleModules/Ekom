<?php


namespace Module\Ekom\Utils\Checkout;


use Core\Services\Hooks;
use Kamille\Architecture\Response\ResponseInterface;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Exception\EkomException;
use Module\Ekom\Session\EkomSession;
use Module\Ekom\Utils\Checkout\Step\CheckoutStepInterface;

/**
 *
 * Basic idea
 * -------------
 *
 * This class is an object to which you bind steps (CheckoutStepInterface).
 * Then it returns a model representing a checkout process with all the steps attached to it.
 *
 * Example steps are: login, billing, shipping, carrier, payment, ...
 *
 *
 * Internally, this class uses different storage:
 *
 * - the CurrentCheckoutData object (see CurrentCheckoutData class notes for more info) is used
 *          to store the step data (explained below).
 *          Using CurrentCheckoutData ensures that modules will be able to access the checkout data
 *          when we wrap up the checkout process and create an order (in the db) out of it.
 *
 * - the steps part of the checkoutPageModel (explained below) is (also) stored in the session
 *          (using a reserved namespace), as we need to "remember" the user's progress during
 *          the different steps of the checkout process (what step is the current step? for instance)
 *
 *
 *
 *
 *
 * //--------------------------------------------
 * // checkoutPageModel
 * //--------------------------------------------
 * - cartModel: the cartModel, defined at the top of CartLayer
 * - steps: array of stepItem, each of which:
 *      - label: string
 *      - isCurrent: bool, whether or not this step currently has the focus
 *      - isDone: bool, whether or not the step was successfully completed by the user
 *      - model: array, depends on the step (the model is only displayed if the step
 *                  is the current one)
 *
 *
 *
 *
 *
 * stepData
 * -----------------
 * stepData is the data collected by the step when its form is successfully submitted.
 * This data will be accessible later by modules.
 *
 * The stepData is also re-injected in the step's form when available.
 *
 *
 * context
 * ------------
 * The ensemble of variables from which steps should take their data from.
 * Like a pool if you will.
 * Typically, it's the $_POST array, or a merge between $_GET, $_POST and $_FILES.
 *
 *
 *
 */
class CheckoutPageUtil
{


    /**
     * @ling-deprecated
     * Allows us to switch between the checkoutLayer and the estimateCheckoutLayer
     * without actually changing the code (when possible) of the json/gscp services.
     *
     * See inside AjaxHandlerLayer, called by Hooks.Ekom_configureCheckoutLayerProvider
     */
    protected $checkoutProvider;
    private $steps; // step objects
    private $stepItems; // the steps part of the checkoutPageModel, coming from session and built during the getModel method
    private $sessionName = "Ekom_CheckoutPageUtil";


    public function __construct()
    {
        $this->steps = [];
    }

    public static function create()
    {
        return new static();
    }


    public function registerStep($name, CheckoutStepInterface $step, $position)
    {
        $this->steps[] = [$name, $step, $position];
        return $this;
    }

    /**
     * @param array|null $context ,
     *                  If null, the context will be a mix of: get, post and files php super arrays
     *                  If array, the context is set to this array.
     *
     *
     *
     *
     * @return array, the checkoutPageModel defined at the top of this class
     * @throws \Exception
     */
    public function getModel(array $context = null)
    {
        if (false === CurrentCheckoutData::isStarted()) {
            Hooks::call("Ekom_CheckoutPageUtil_onCheckoutNewSession");
            CurrentCheckoutData::started();
        }


        if ($this->steps) {


            // order step by ascending position
            $steps = $this->steps;
            usort($steps, function ($stepA, $stepB) {
                return $stepA[2] < $stepB[2];
            });


            //--------------------------------------------
            // WHICH STEP TO DISPLAY NOW?
            //--------------------------------------------
            /**
             * Either the user clicked on a step, or otherwise we use the natural algorithm
             */
            $clickedStep = (array_key_exists("_step", $_GET)) ? $_GET['_step'] : null;


            // prepare context
            /**
             * @todo-ling, make this work with ajax, so get the $_FILES from ajax?
             */
            if (null === $context) {
                $context = array_replace($_GET, $_POST, $_FILES);
            }
            // unset all variables prefixed with _?
            unset($context['_step']);


            $currentStep = null;
            $isCompleted = false;
            if (null === $clickedStep) {
                $currentStep = $this->getFirstNonDoneStep($steps);
                if (null === $currentStep) {
                    $this->onAllStepsCompleted();
                    $isCompleted = true;
                    // $currentStep is still null
                } else {


                    /**
                     * If the step form is submitted, we save its data and
                     * select the VERY NEXT step (because that's what the user expects)
                     */
                    $stepData = $this->getStepData($currentStep);
                    $stepObject = $this->getStepObject($currentStep);
                    $stepObject->prepare($stepData, $context);

                    if (true === $stepObject->isSuccessfullyPosted()) {
                        $stepData = $stepObject->getStepData();

                        $this->saveStep($currentStep, $stepData);
                        $currentStep = self::getNextStep($currentStep, $steps);
                        if (null === $currentStep) {
                            $this->onAllStepsCompleted();
                            $isCompleted = true;
                            // $currentStep is still null
                        }
                    }
                }
            } else {
                $currentStep = $clickedStep;
            }


            if (false === $isCompleted) {
                //--------------------------------------------
                // NOW DISPLAYING THE CURRENT STEP's MODEL
                //--------------------------------------------
                if (null !== $currentStep) {
                    $this->setCurrentStep($currentStep);


                    $stepData = $this->getStepData($currentStep);
                    $stepObject = $this->getStepObject($currentStep);
                    $stepObject->prepare($stepData, $context);
                    $model = $stepObject->getFormModel();
                    $this->setStepItemValue($currentStep, "model", $model);

                } else {
                    /**
                     * This means that all steps are completed
                     */
                }

            }
        }


        return [
            'steps' => $this->getStepItemsFromSession(),
            'cartModel' => EkomApi::inst()->cartLayer()->getCartModel(),
        ];

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
     *          If false, it can mean one of two things:
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

    private function onAllStepsCompleted()
    {
        /**
         * Modules? do something?
         */
        $this->setCurrentStep(false); // ensure that no step is current
    }


    private function setStepData($stepName, array $data)
    {
        /**
         * We use the CurrentCheckoutData util to store all the step data,
         * so that they are statically available later in the checkout process
         * (for instance modules will need details of the payment method chosen by the user).
         *
         */
        $allData = CurrentCheckoutData::get("CheckoutPageUtil", []);
        $allData[$stepName] = $data;
        CurrentCheckoutData::set("CheckoutPageUtil", $allData);
    }

    private function getStepData($stepName)
    {
        $allData = CurrentCheckoutData::get("CheckoutPageUtil", []);
        if (array_key_exists($stepName, $allData)) {
            return $allData[$stepName];
        }
        return [];
    }

    private function saveStep($stepName, array $stepData)
    {
        $this->setStepData($stepName, $stepData);
        $this->setStepItemValue($stepName, "isDone", true);
    }


    private function getStepItemsFromSession()
    {
        $stepItems = EkomSession::get($this->sessionName);
        if (null === $stepItems) {

            /**
             * It's expected that we prepare ALL items before hand
             */
            $stepItems = [];
            foreach ($this->steps as $stepName => $info) {

                /**
                 * @var $step CheckoutStepInterface
                 */
                $step = $info[1];
                $stepItems[$stepName] = [
                    "label" => $step->getLabel(),
                    "isDone" => false,
                    "isCurrent" => false,
                    "model" => [],
                ];
            }
            EkomSession::set($this->sessionName, $stepItems);
        }
        return $stepItems;
    }


    private function setStepItemValue($stepName, $key, $value)
    {
        $item = $this->getStepItem($stepName);
        $item[$key] = $value;
        $this->setStepItem($stepName, $item);
    }

    private function setStepItem($stepName, array $item)
    {
        $items = $this->getStepItemsFromSession();
        $items[$stepName] = $item;
        EkomSession::set($this->sessionName, $items);
    }

    private function getStepItem($name)
    {
        $items = $this->getStepItemsFromSession();
        if (array_key_exists($name, $items)) {
            return $items[$name];
        }
        throw new EkomException("Invalid step name: " . $name);
    }

    /**
     * @param $stepName
     * @return CheckoutStepInterface
     */
    private function getStepObject($stepName)
    {
        $item = $this->getStepItem($stepName);
        return $item[1];
    }


    private function setCurrentStep($stepName)
    {
        $items = $this->getStepItemsFromSession();
        foreach ($items as $k => $item) {
            if ($stepName === $k) {
                $item['isCurrent'] = true;
            } else {
                $item['isCurrent'] = false;
            }
        }
        EkomSession::set($this->sessionName, $items);
    }


    /**
     * @return string|null, the name of the first non done step,
     *                  or false if all steps are completed.
     */
    private function getFirstNonDoneStep(array $orderedSteps)
    {
        /**
         * Natural step choosing algorithm
         */
        foreach ($orderedSteps as $info) {
            $stepName = $info[0];
            $stepItem = $this->getStepItem($stepName);
            if (false === $stepItem['isDone']) {
                return $stepName;
            }
        }
        return null;
    }

}