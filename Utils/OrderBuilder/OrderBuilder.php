<?php


namespace Module\Ekom\Utils\OrderBuilder;


use Module\Ekom\Utils\OrderBuilder\Step\OrderBuilderStepInterface;


class OrderBuilder
{

    /**
     * @var OrderBuilderStepInterface[]
     */
    protected $steps;
    protected $context;


    public function __construct()
    {
        $this->steps = [];
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

    public function getOrderModel() // override me
    {
        return [];
    }


    public function getStepsInfo()
    {
        $ret = [];
        $activeFound = false;
        $model = null;
        $activeId = null;
        $userStep = $this->getUserStep();

        // parse the steps
        foreach ($this->steps as $id => $step) {
            if (
                false === $activeFound &&
                false === $step->isDone()
            ) {
                $activeFound = true;
                $model = $step->listen();
                $activeId = $id;
                if (true === $step->isDone()) {
                    $activeFound = false;
                }
            }
        }


        // compile information for the view
        foreach ($this->steps as $id => $step) {


            if (null === $userStep) {
                if ($activeId === $id) {
                    $state = 'active';
                } else {
                    if (true === $step->isDone()) {
                        $state = 'done';
                    } else {
                        $state = 'inactive';
                    }
                }
            }
            else{
                if ($userStep === $id) {
                    $state = 'active';
                } else {
                    if (true === $step->isDone()) {
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
        foreach ($this->steps as $id => $step) {
            if (false === $step->isDone()) {
                return false;
            }
        }
        return true;
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


}