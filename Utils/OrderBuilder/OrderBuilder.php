<?php


namespace Module\Ekom\Utils\OrderBuilder;


use Module\Ekom\Session\EkomSession;
use Module\Ekom\Utils\OrderBuilder\Step\OrderBuilderStepInterface;


class OrderBuilder implements OrderBuilderInterface
{

    /**
     * @var OrderBuilderStepInterface[]
     */
    protected $steps;
    protected $sessionName;


    public function __construct()
    {
        $this->steps = [];
        $this->sessionName = "order-builder-steps";
    }

    public static function create()
    {
        return new static();
    }


    public function registerStep($id, OrderBuilderStepInterface $step)
    {
        $this->steps[$id] = $step;
        return $this;
    }

    public function getOrderModel() // override me
    {
        return [];
    }

    public function clean()
    {
        foreach ($this->steps as $id => $step) {
            $step->clean();
        }
        $this->cleanStepsDone();
    }


    public function getStepsInfo()
    {
        $ret = [];
        $activeFound = false;
        $model = null;

        $activeId = null;
        $userStep = $this->getUserStep();
        $sessionSteps = EkomSession::get($this->sessionName, []);


        // parse the steps
        foreach ($this->steps as $id => $step) {


            if (false === $step->isRelevant()) {
                continue;
            }

            if (null === $userStep) {
                /**
                 * Leave done alone,
                 * first non done becomes active
                 */
                if (
                    false === $activeFound &&
                    false === $this->stepIsDone($id, $sessionSteps)
                ) {
                    $activeFound = true;
                    $activeId = $id;
                    $done = false;
                    $model = $step->listen($this, $done);
                    $sessionSteps = $this->handleDone($id, $done);
                    if (true === $this->stepIsDone($id, $sessionSteps)) {
                        $activeFound = false;
                        $activeId = null;
                    }
                }
            } else {
                /**
                 * Leave done alone,
                 * the step clicked by the user becomes active
                 */
                if ($userStep === $id) {
                    $done = false;
                    $model = $step->listen($this, $done);
                    $activeId = $id;
                }
            }
        }


        // compile information for the view
        foreach ($this->steps as $id => $step) {
            if (false === $step->isRelevant()) {
                $state = 'irrelevant';
            } else {


                if ($activeId === $id) {
                    $state = 'active';
                } else {
                    if (true === $this->stepIsDone($id, $sessionSteps)) {
                        $state = 'done';
                    } else {
                        $state = 'inactive';
                    }
                }
            }

            $Id = ucfirst($id);
            $ret['id' . $Id] = $id;
            $ret['state' . $Id] = $state;
        }
        $ret['model'] = $model;
        return $ret;
    }


    public function isCompleted()
    {
        $sessionSteps = EkomSession::get($this->sessionName, []);
        foreach ($this->steps as $id => $step) {
            if (false === $this->stepIsDone($id, $sessionSteps)) {
                return false;
            }
        }
        return true;
    }


    //--------------------------------------------
    //
    //--------------------------------------------
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
    private function stepIsDone($id, array $sessionSteps)
    {
        return array_key_exists($id, $sessionSteps);
    }

    private function handleDone($id, $done)
    {
        $sessionSteps = EkomSession::get($this->sessionName, []);
        if (true === $done) {
            $sessionSteps[$id] = true;
            EkomSession::set($this->sessionName, $sessionSteps);
        }
        return $sessionSteps;
    }

    private function cleanStepsDone()
    {
        EkomSession::remove($this->sessionName);
    }

}