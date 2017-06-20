<?php


namespace Module\Ekom\Laws\DynamicWidgetBinder\Attribute2TemplateAdaptor;


class Attribute2TemplateAdaptor implements Attribute2TemplateAdaptorInterface
{
    public function getTemplate($attributeName)
    {
        return "Ekom/ListModifier/AttributeSelector/GenericAttributeSelector/default";
//        return "Ekom/ListModifier/AttributeSelector/" . ucfirst($attributeName) . "AttributeSelector/default";
    }
}