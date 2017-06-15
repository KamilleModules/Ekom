<?php


namespace Module\Ekom\Price\PriceChain;


class PriceChain implements PriceChainInterface
{

    private $nodes;
    private $transformers;
    private $history;
    protected $historyBaseName;

    public function __construct()
    {
        $this->nodes = [];
        $this->transformers = [];
        $this->history = [];
        $this->historyBaseName = "_price";
    }


    public static function create()
    {
        return new static();
    }


    public function addNode($node)
    {
        $this->nodes[] = $node;
        return $this;
    }

    /**
     * @param $node
     * @param $transformer
     * @param null $nodeName , used for history
     * @return $this
     */
    public function addTransformer($node, $transformer, $transformerName = null, $order = 0)
    {
        $this->transformers[$node][] = [$transformer, $transformerName, $order];
        return $this;
    }

    public function removeTransformers($node, $transformerName = null)
    {
        if (null === $transformerName) {
            unset($this->nodes[$node]);
        } else {
            if (array_key_exists($node, $this->nodes)) {
                $tr = $this->nodes[$node];
                foreach ($tr as $k => $info) {
                    list($t, $name, $order) = $info;
                    if ($name === $transformerName) {
                        unset($this->nodes[$node][$k]);
                    }
                }
            }
        }
    }

    public function run($price, array &$model)
    {

        $this->mark($this->historyBaseName, $price);
        foreach ($this->nodes as $node) {
            if (array_key_exists($node, $this->transformers)) {
                $tr = $this->transformers[$node];
                usort($tr, function ($infoA, $infoB) {
                    return (int)($infoA[2] > $infoB[2]);
                });

                $c = 0;
                foreach ($tr as $info) {
                    list($t, $name, $order) = $info;
                    if (null === $name) {
                        $name = $c++;
                    }
                    if (is_callable($t)) {
                        $price = call_user_func_array($t, [$price, &$model]);
                    } else {
                        // could be object?
                        throw new \Exception("Not implemented yet");
                    }
                    $this->mark($node . ":" . $name, $price);
                }
            }
        }
        return $price;
    }

    public function getHistory()
    {
        return $this->history;
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    private function mark($name, $price)
    {
        $this->history[] = [$name, $price];
    }

}