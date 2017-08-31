<?php


namespace Module\Ekom\Chip\Country;


class CountryChip
{
    private $iso_code; // mandatory

    public function __construct()
    {
        $this->iso_code = null;
    }


    public static function create()
    {
        return new static();
    }

    /**
     * @return null
     */
    public function getIsoCode()
    {
        return $this->iso_code;
    }

    public function setIsoCode($iso_code)
    {
        $this->iso_code = $iso_code;
        return $this;
    }


}