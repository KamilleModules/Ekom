<?php


namespace Module\Ekom\Models;


/**
 * This class is not used by the code, except as a documentation reference.
 */
class EkomModels{


    /**
     * addressModel
     * ==================
     * - address_id
     * - first_name
     * - last_name
     * - phone
     * - address
     * - city
     * - postcode
     * - supplement
     * - country
     * - country_id
     * - country_iso_code
     * //
     * - is_default_shipping_address, bool
     * - is_default_billing_address, bool
     * - fName, string: a full name, which format depends on some locale parameters
     * - fAddress, string: a full address, which format depends on some locale parameters
     *
     *
     */
    private function addressModel(){
        return [];
    }


    /**
     * shopPhysicalAddress
     * =====================
     * - id
     * - first_name
     * - last_name
     * - phone
     * - address
     * - city
     * - postcode
     * - supplement
     * - active
     * - country
     * - country_iso_code: the country iso code
     * - country: the country label
     *
     *
     */
    private function shopPhysicalAddress(){

    }
}