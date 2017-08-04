<?php


namespace Module\Ekom\Models\Attribute;


class AttributeModel
{


    //
    private $name;
    private $nameId;
    private $nameLabel;
    //
    private $value;
    private $valueId;
    private $valueLabel;


    public static function create()
    {
        return new static();
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNameId()
    {
        return $this->nameId;
    }

    public function setNameId($nameId)
    {
        $this->nameId = $nameId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNameLabel()
    {
        return $this->nameLabel;
    }

    public function setNameLabel($nameLabel)
    {
        $this->nameLabel = $nameLabel;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValueId()
    {
        return $this->valueId;
    }

    public function setValueId($valueId)
    {
        $this->valueId = $valueId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValueLabel()
    {
        return $this->valueLabel;
    }

    public function setValueLabel($valueLabel)
    {
        $this->valueLabel = $valueLabel;
        return $this;
    }


}