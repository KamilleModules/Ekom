<?php


namespace Module\Ekom\ProductBox\AttributesModel\GeneratorFactory;


use Module\Ekom\Exception\EkomException;
use Module\Ekom\ProductBox\AttributesModel\Generator\AttributesModelGeneratorInterface;

class AttributesModelGeneratorFactory implements AttributesModelGeneratorFactoryInterface
{

    private $generatorsGetters;
    private $defaultGenerator;


    public function __construct()
    {
        $this->generatorsInfo = [];
        $this->defaultGenerator = null;
    }


    public function get(array $generatorInfo)
    {
        foreach ($this->generatorsGetters as $getter) {
            $res = call_user_func($getter, $generatorInfo);
            if ($res instanceof AttributesModelGeneratorInterface) {
                return $res;
            }
        }
        if (null !== $this->defaultGenerator) {
            return $this->defaultGenerator;
        }
        throw new EkomException("default generator not set");
    }



    //--------------------------------------------
    //
    //--------------------------------------------
    /**
     * @param callable $getGenerator
     *                  null|AttributesModelGeneratorInterface  function ( arr:generatorInfo )
     *                  generatorInfo, see this interface.get method for more info
     *
     *
     * @return $this
     */
    public function setGenerator(callable $getGenerator)
    {
        $this->generatorsGetters[] = $getGenerator;
        return $this;
    }

    public function setDefaultGenerator(AttributesModelGeneratorInterface $defaultGenerator)
    {
        $this->defaultGenerator = $defaultGenerator;
        return $this;
    }


}