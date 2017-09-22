<?php


namespace Module\Ekom\Utils\Stepper\Step;


interface StepInterface
{


    /**
     * set the data for the step to display
     * (data of the form)
     */
    public function setData(array $data);

    public function getData();

    public function render();

    public function isPosted();

    public function isValid();
}