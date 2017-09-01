<?php


namespace Module\Ekom\Chip\Country;




class CountryChip
{

    private $iso_code;



    public function __construct()
    {
        $this->iso_code = '';

    }


    public static function create()
    {
        return new static();
    }

    
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