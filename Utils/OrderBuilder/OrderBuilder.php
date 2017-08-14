<?php


namespace Module\Ekom\Utils\OrderBuilder;


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
class OrderBuilder implements OrderBuilderInterface
{

    /**
     * @var OrderBuilderStepInterface[]
     */
    protected $steps;
    protected $context;
    private $sessionName;


    public function __construct()
    {
        $this->steps = [];
        $shopId = E::getShopId();
        $this->sessionName = 'order-builder-' . $shopId;
        $this->context = null;
    }

    public static function create()
    {
        return new static();
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

    public function setStepData($step, $data)
    {
        $all = $this->getSessionData();
        $all[$step] = $data;
        return $this;
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


        //--------------------------------------------
        // Default algorithm for states
        //--------------------------------------------
        $newStates = [];
        foreach ($states as $id => $state) {
            /**
             * @var $step OrderBuilderStepInterface
             */
            $step = $this->steps[$id];
            if (true === $step->isRelevant($context)) {


                if (false === $activeFound) {
                    if (
                    in_array($state, ['active', 'inactive'])
                    ) {
                        $state = 'active';
                        $activeFound = true;

                        $justDone = false;
                        $model = $step->process($context, $justDone);
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
            } else {
                $state = 'irrelevant';
            }
            $newStates[$id] = $state;
        }


        //--------------------------------------------
        // The user can navigate steps back/forth
        //--------------------------------------------
        /**
         * Note: the user can only go back/forth to done steps.
         */
        $step = $this->getUserStep();
        if(null!==$step){

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
                $conf['data'][$id] = null;
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