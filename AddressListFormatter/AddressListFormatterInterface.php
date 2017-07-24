<?php


namespace Module\Ekom\AddressListFormatter;

/**
 * Adapts a raw model to a polished model, usable by templates.
 */
interface AddressListFormatterInterface
{
    /**
     * @param array $rows, as returned by the UserLayer.getAddressList method.
     * @return array, depends on the formatter.
     *
     */
    public function format(array $rows);
}