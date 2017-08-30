<?php


namespace Module\Ekom\Chip;


use Module\Ekom\Chip\Exception\ChipException;

class ChipProcessor
{


    protected function exception(\Exception $e)
    {
        throw $e;
    }

    protected function error($msg)
    {
        throw new ChipException($msg);
    }


    protected function mandatory($value, $name)
    {
        if (null === $value) {
            $this->error("$name not set");
        }
        return $value;
    }
}