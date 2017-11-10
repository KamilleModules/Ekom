<?php


namespace Module\Ekom\Utils\DistanceEstimator;


class EkomDistanceEstimator implements DistanceEstimatorInterface
{


    public function estimate(array $addressOne, array $addressTwo)
    {
        $countryOne = $addressOne['country_iso_code'];
        $countryTwo = $addressTwo['country_iso_code'];
        if ($countryOne === $countryTwo) {
            /**
             * Todo?: estimate distance between cities
             */
            return 1000;
        }
        /**
         * Todo: estimate distance between countries
         */
        return 5000;
    }
}