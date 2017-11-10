<?php


namespace Module\Ekom\Exception;


class EkomInvalidArgumentException extends EkomException
{
    protected $missingKey;

    public static function create()
    {
        return new static();
    }

    /**
     * @return mixed
     */
    public function getMissingKey()
    {
        return $this->missingKey;
    }

    public function setMissingKey($missingKey)
    {
        $this->missingKey = $missingKey;
        return $this;
    }
}