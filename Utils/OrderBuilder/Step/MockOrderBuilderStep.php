<?php


namespace Module\Ekom\Utils\OrderBuilder\Step;


class MockOrderBuilderStep implements OrderBuilderStepInterface
{

    private $identifier;
    private $_isRelevant;


    public function __construct()
    {
        $this->identifier = "mock_step";
        $this->_isRelevant = true;
    }

    public static function create()
    {
        return new static();
    }

    public function process($context, &$justDone = false)
    {
        if (array_key_exists($this->identifier, $_POST)) {
            $justDone = true;
        } else {
            return [
                $this->identifier . 'ModelName' => $this->identifier,
                $this->identifier . 'ModelColor' => 'blue',
            ];
        }
    }

    public function isRelevant($context)
    {
        return $this->_isRelevant;
    }

    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
        return $this;
    }

    public function setIsRelevant($isRelevant)
    {
        $this->_isRelevant = $isRelevant;
        return $this;
    }


}