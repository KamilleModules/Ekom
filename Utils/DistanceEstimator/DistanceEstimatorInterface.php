<?php


namespace Module\Ekom\Utils\DistanceEstimator;


interface DistanceEstimatorInterface
{


    /**
     * Estimate the distance in km between the city of address one and the city of address two.
     * 0 is returned if the two addresses are located in the same city.
     * The maximum number should be less than 20000.
     *
     * (according to google, the farthest places in the world have a distance of 19,996km between them)
     *
     *
     *
     *
     * Both addresses are arrays which contain the following:
     *
     * - city
     * - postcode
     * - country_id
     * - country_iso_code
     *
     *
     * @param array $addressOne
     * @param array $addressTwo
     * @return int
     */
    public function estimate(array $addressOne, array $addressTwo);
}