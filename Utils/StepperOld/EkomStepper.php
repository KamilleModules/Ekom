<?php


namespace Module\Ekom\Utils\Stepper;


use Module\Ekom\Session\EkomSession;
use Module\Ekom\Utils\Stepper\Step\StepInterface;

class EkomStepper
{

    private $defaultCurrentStep;
    private $steps;


    public function __construct()
    {
        $this->defaultCurrentStep = 1;
        $this->steps = [];
    }


    public static function create()
    {
        return new static();
    }

    public function addStep($name, StepInterface $step)
    {
        $this->steps[] = [$name, $step];
        return $this;
    }


    public function render()
    {
        $info = $this->getSessionInfo();
        $data = $info['data'];
        $currentStep = (int)$info['currentStep'];


        $currentIndex = 1;
        foreach ($this->steps as $k => $stepInfo) {

            list($name, $step) = $stepInfo;

            if ($currentIndex === $currentStep) {
                $res = $this->processStep($step, $data[$name]);
                if (true === $res) {
                    $this->registerStepValid($name, $step);

                    // is there a next step?
                    if (array_key_exists($k + 1, $this->steps)) {
                        $this->registerCurrentStep($k + 1);

                        // display next step right away
                        $nextStepData = $data[$this->steps[$k + 1][0]];
                        $nextStep = $this->steps[$k + 1][1];
                        $nextStep->setData($nextStepData);
                        $nextStep->render();

                    } else {
                        $this->success();
                    }

                }
            }
            $currentIndex++;
        }
    }



    //--------------------------------------------
    //
    //--------------------------------------------
    private function success()
    {

    }

    private function registerCurrentStep($number)
    {
        $info = EkomSession::get("ekomCheckoutStepper");
        $info['currentStep'] = $number;
        EkomSession::set("ekomCheckoutStepper", $info);
    }

    private function processStep(StepInterface $step, array $data)
    {
        if ($step->isPosted()) {
            if ($step->isValid()) {
                return true;
            }
        } else {
            $step->setData($data);
        }
        $step->render();
    }

    private function getSessionInfo()
    {
        $info = EkomSession::get("ekomCheckoutStepper");

        // initializing info array
        if (null === $info) {
            $data = [];
            $done = [];
            foreach ($this->steps as $stepInfo) {
                list($name, $step) = $stepInfo;
                $data[$name] = [];
                $done[$name] = false;
            }
            $info = [
                'data' => $data,
                'done' => $done,
                'currentStep' => $this->defaultCurrentStep,
            ];
            EkomSession::set("ekomCheckoutStepper", $info);
        }
        return $info;
    }

    private function registerStepValid($name, StepInterface $step)
    {
        $info = EkomSession::get("ekomCheckoutStepper");
        $info['data'][$name] = $step->getData();
        $info['done'][$name] = true;
        EkomSession::set("ekomCheckoutStepper", $info);
    }
}