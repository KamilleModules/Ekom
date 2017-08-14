<?php


namespace Module\Ekom\Utils\OrderBuilder;


use Kamille\Services\XLog;
use Module\Ekom\Session\EkomSession;
use Module\Ekom\Utils\E;
use Module\Ekom\Utils\OrderBuilder\Step\OrderBuilderStepInterface;

/**
 * This class stores step data and step states into the session.
 * It looks like this:
 *
 * SESSION:
 *  - $sessionName:
 *      - states:
 *          - $stepName: $state
 *          - $stepName: $state
 *          - ...
 *      - data:
 *          - $stepName: $data
 *          - $stepName: $data
 *          - ...
 *
 *
 *
 *
 *
 * Note: with this implementation, all steps MUST BE registered BEFORE other any useful method
 * can be used.
 *
 *
 */
class OrderBuilderOld implements OrderBuilderInterface
{

    /**
     * @var OrderBuilderStepInterface[]
     */
    protected $steps;
    protected $context;
    private $sessionName;
    private $exceptionCounter;


    public function __construct()
    {
        $this->steps = [];
        $shopId = E::getShopId();
        $this->sessionName = 'order-builder-' . $shopId;
        $this->context = null;
        $this->exceptionCounter = 0;
    }

    public static function create()
    {
        return new static();
    }

    public function setSessionName($sessionName)
    {
        $this->sessionName = $sessionName;
        return $this;
    }

    public function getContext()
    {
        return $this->context;
    }


    public function registerStep($id, OrderBuilderStepInterface $step)
    {
        $this->steps[$id] = $step;
        return $this;
    }

    public function setStepData($step, array $data)
    {
        $all = $this->getSessionData();
        $all[$step] = $data;
        return $this;
    }

    public function getStepData($step)
    {
        $stepData = $this->getSessionData();
        if (array_key_exists($step, $stepData)) {
            return $stepData[$step];
        }
        return [];
    }

    public function getOrderModel() // override me
    {
        return [];
    }


    public function getStepsInfo()
    {
        $this->initSession();
        $ret = [];
        $model = null;
        $states = $this->getSessionStates();
        $activeFound = false;
        $context = $this->getContext();
        $userStep = $this->getUserStep();


        try {


            //--------------------------------------------
            // Default algorithm for states
            //--------------------------------------------
            $newStates = [];
            /**
             * flag used only if the user manually chose her step
             * (step != null)
             */
            $userStepWasReached = false;
            foreach ($states as $id => $state) {

                if (array_key_exists($id, $this->steps)) {


                    /**
                     * @var $step OrderBuilderStepInterface
                     */
                    $step = $this->steps[$id];
                    if (true === $step->isRelevant($context)) {


                        if ('done' === $state && $id === $userStep) {

                            $stepData = $this->getStepData($id);
                            $state = 'active';
                            $justDone = false;
                            $model = $step->process($this, $justDone, $stepData);
                            /**
                             * We don't care of the justDone flag now, because
                             * we know the user wants to display the form and
                             * she possibly can't have posted it successfully yet.
                             */
                            $userStepWasReached = true;
                            $activeFound = true;

                        } else {

                            if (
                                true === $userStepWasReached &&
                                'active' === $state
                            ) {
                                $state = 'inactive';
                            } else {


                                if (false === $activeFound) {
                                    if (
                                    in_array($state, ['active', 'inactive'])
                                    ) {
                                        $state = 'active';
                                        $activeFound = true;

                                        $justDone = false;
                                        $stepData = $this->getStepData($id);
                                        $model = $step->process($this, $justDone, $stepData);
                                        if (true === $justDone) {
                                            $activeFound = false;
                                            $state = "done";
                                        }
                                    }
                                } else {
                                    if (!in_array($state, ['done'])) {
                                        $state = 'inactive';
                                    }
                                }
                            }
                        }
                    } else {
                        $state = 'irrelevant';
                    }
                } else {
                    throw new \Exception("Step not set: $id");
                }
                $newStates[$id] = $state;
            }

        } catch (\Exception $e) {
            /**
             * This is a quick'n'dirty work around for the problem
             * of the long time disconnected user who refreshes the page on the
             * checkout page, and one of the step throws an exception because she's
             * not connected.
             */
            if (0 === $this->exceptionCounter++) {
                XLog::error("[Ekom module] - OrderBuilder: $e");
                $this->clean();
                return $this->getStepsInfo();

            } else {
                throw $e;
            }
        }

        //--------------------------------------------
        // assembling data
        //--------------------------------------------
        foreach ($newStates as $id => $state) {
            $Id = ucfirst($id);
            $ret['id' . $Id] = $id;
            $ret['state' . $Id] = $state;
        }
        $all = EkomSession::get($this->sessionName, []);
        $all['states'] = $newStates;
        EkomSession::set($this->sessionName, $all);


        $ret['model'] = $model;
        return $ret;
    }

    public function clean()
    {
        $this->initSession();
        EkomSession::remove($this->sessionName);
        return $this;
    }

    public function isCompleted()
    {
        $states = $this->getSessionStates();
        foreach ($states as $id => $state) {
            if (!in_array($state, ['done', 'irrelevant'])) {
                return false;
            }
        }
        return true;
    }





    //--------------------------------------------
    //
    //--------------------------------------------
    protected function getSessionData()
    {
        $this->initSession();
        $all = EkomSession::get($this->sessionName, []);
        if (array_key_exists('data', $all)) {
            return $all['data'];
        }
        return null;
    }

    /**
     * @return null|string, the step called manually by the user
     */
    protected function getUserStep()
    {
        if (array_key_exists('step', $_POST)) {
            return $_POST['step'];
        }
        return null;
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    private function initSession()
    {
        if (false === EkomSession::has($this->sessionName)) {
            $conf = [
                'states' => [],
                'data' => [],
            ];

            $context = $this->context;
            foreach ($this->steps as $id => $step) {
//                if (false === $step->isRelevant($context)) {
//                    $state = 'irrelevant';
//                } else {
//                    $state = 'inactive';
//                }
                $state = 'inactive';
                $conf['states'][$id] = $state;
                $conf['data'][$id] = [];
            }

            EkomSession::set($this->sessionName, $conf);
        }
    }

    private function getSessionStates()
    {
        $this->initSession();
        $all = EkomSession::get($this->sessionName, []);
        return $all['states'];
    }
}