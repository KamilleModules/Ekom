<?php


namespace Module\Ekom\SokoForm\Control;


use SokoForm\Control\SokoInputControl;

class EkomSokoDateControl extends SokoInputControl
{
    public function __construct()
    {
        parent::__construct();
        $this->setProperties([
            'date' => true,
            'useTime' => false,
            /**
             * @todo-ling: set the correct lang here...
             */
            'lang' => "fr",
        ]);
    }


    public function useDatetime()
    {
        $this->properties['useTime'] = true;
        return $this;
    }


}