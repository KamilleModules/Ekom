<?php


namespace Module\Ekom\AddressListFormatter;


use Module\Ekom\Exception\EkomException;

class AddressListFormatter implements AddressListFormatterInterface
{

    /**
     *
     * In this model, you can have multiple billing addresses, and
     * multiple shipping addresses.
     * Addresses of both types are returned together in a merged array,
     * each address looks like this:
     *
     * - title: combination of last_name and first_name
     * - address_line_1: the address bulk
     * - address_line_2: city and postcode
     * - address_line_3: country
     * - phone:
     * - is_shipping_default: bool
     * - is_billing_default: bool
     *
     *
     */
    public function format(array $rows)
    {
        $ret = [];
        $billingDefault = null;
        $shippingDefault = null;


        // note, rows are ordered by "order asc" per definition
        foreach ($rows as $row) {

//            $row['title'] = ucfirst($row['first_name']) . " " . ucfirst($row['last_name']);
            $row['title'] = ucfirst($row['libelle']);
            $row['address_line_1'] = $row['address'];
            $row['address_line_2'] = ucfirst($row['city']) . ', ' . $row['postcode'];
            $row['address_line_3'] = $row['country'];
            $row['is_shipping_default'] = (bool)$row['is_default_shipping_address'];
            $row['is_billing_default'] = (bool)$row['is_default_billing_address'];
            $ret[] = $row;

        }


        return $ret;
    }
}