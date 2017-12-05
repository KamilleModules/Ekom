<?php


namespace Module\Ekom\Utils\BalancedSchedule;


class Bucket
{
    protected $amount;
    private $name;


    public function __construct()
    {
        $this->amount = 0;
        $this->name = null;
    }

    public static function create()
    {
        return new static();
    }

    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $requestedAmount
     * @param null $id , null if equilibrium phase
     * @param null $date , null if equilibrium phase
     * @return int
     */
    public function capture($requestedAmount, $id = null, $date = null)
    {
        if ($this->amount >= $requestedAmount) { // we've got it, no problem
            $this->amount -= $requestedAmount;
            return $requestedAmount;
        } else {
            // you're asking too much, we give you only what's left
            // (and we get empty for the rest of the party)
            $remainingAmount = $this->amount;
            $this->amount = 0;
            return $remainingAmount;
        }

    }
}
