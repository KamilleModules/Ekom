<?php


namespace Module\Ekom\Utils\Api;


use Bat\ArrayTool;
use Module\Ekom\Exception\EkomException;

class EkomApiLink
{
    public static function create()
    {
        return new static();
    }



    protected function handleMissing($mandatories, $data)
    {
        $missing = ArrayTool::getMissingKeys($data, $mandatories);
        if (false !== $missing) {
            throw new EkomException("The following keys are missing: " . implode(', ', $missing));
        }
    }

    protected function get($k, array $data, $default = "")
    {
        if (array_key_exists($k, $data)) {
            return $data[$k];
        }
        return $default;
    }
}