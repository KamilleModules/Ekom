<?php


namespace Module\Ekom\Laws\DynamicWidgetBinder\Attribute2TemplateAdaptor;


interface Attribute2TemplateAdaptorInterface
{
    /**
     *
     * Return the template of the AttributeSelector widget, to use in a laws widget configuration file
     * to display the choice of attributes.
     *
     *
     * An attribute selector widget is a widget which configuration contains an attributes key containing an array of attribute name => attributes.
     * Each entry is an array that contains at least the following keys:
     *
     * - product_id: the product id
     * - name: the name of the attribute
     * - name_label: the name of the attribute, in the lang of the application instance
     * - value: the value of the attribute
     * - value_label: the value of the attribute, in the lang of the application instance
     * - attribute_id: int, the id of the attribute
     * - value_id: int, the id of the value
     * - uri: the current uri with the params set as if this attribute was clicked (and so the attribute value is added)
     * - count: int, the number of products that have this combination of attribute-attribute value
     *
     * @return string|false, the template to use in the widget configuration
     *
     */
    public function getTemplate($attributeName);
}