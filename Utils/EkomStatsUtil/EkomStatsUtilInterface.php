<?php


namespace Module\Ekom\Utils\EkomStatsUtil;


interface EkomStatsUtilInterface
{

    /**
     * options:
     *      - ?currency: default=eng, the iso code of the currency
     */
    public function prepare($dateStart = null, $dateEnd = null, array $options = []);


    /**
     * The revenues without tax
     * (Chiffre d'affaires HT)
     * @return float
     */
    public function getRevenues();


}