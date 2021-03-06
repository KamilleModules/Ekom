<?php

$types = [
    'ek_action' => [
        'id' => 'auto_increment',
        'source' => 'input',
        'source2' => 'input',
        'operator' => 'input',
        'target' => 'input',
        'target2' => 'input',
    ],
    'ek_address' => [
        'id' => 'auto_increment',
        'type' => 'input',
        'city' => 'input',
        'postcode' => 'input',
        'address' => 'input',
        'active' => 'switch',
        'state_id' => 'selectForeignKey+query=select id, iso_code from kamille.ek_state+firstOptionLabel=Please choose an option+firstOptionValue=0',
        'country_id' => 'selectForeignKey+query=select id, iso_code from kamille.ek_country',
    ],
    'ek_backoffice_user' => [
        'id' => 'auto_increment',
        'email' => 'input',
        'pass' => 'pass',
        'lang_id' => 'selectForeignKey+query=select id, label from kamille.ek_lang',
    ],
    'ek_carrier' => [
        'id' => 'auto_increment',
    ],
    'ek_carrier_has_action' => [
        'carrier_id' => 'selectForeignKey+query=select id, id from kamille.ek_carrier',
        'action_id' => 'selectForeignKey+query=select id, source from kamille.ek_action',
    ],
    'ek_carrier_has_condition' => [
        'carrier_id' => 'selectForeignKey+query=select id, id from kamille.ek_carrier',
        'condition_id' => 'selectForeignKey+query=select id, type from kamille.ek_condition',
    ],
    'ek_carrier_lang' => [
        'id' => 'auto_increment',
        'label' => 'input',
        'description' => 'textarea',
        'carrier_id' => 'selectForeignKey+query=select id, id from kamille.ek_carrier',
        'lang_id' => 'selectForeignKey+query=select id, label from kamille.ek_lang',
    ],
    'ek_carrier_shop' => [
        'carrier_id' => 'selectForeignKey+query=select id, id from kamille.ek_carrier',
        'shop_id' => 'selectForeignKey+query=select id, label from kamille.ek_shop',
        'active' => 'switch',
    ],
    'ek_cart' => [
        'id' => 'auto_increment',
        'items' => 'textarea',
        'user_id' => 'selectForeignKey+query=select id, email from kamille.ek_user',
    ],
    'ek_category' => [
        'id' => 'auto_increment',
        'label' => 'input',
        'is_active' => 'switch',
        'shop_id' => 'selectForeignKey+query=select id, label from kamille.ek_shop',
        'category_id' => 'selectForeignKey+query=select id, label from kamille.ek_category+firstOptionLabel=Please choose an option+firstOptionValue=0',
    ],
    'ek_category_has_product' => [
        'category_id' => 'selectForeignKey+query=select id, label from kamille.ek_category',
        'product_id' => 'selectForeignKey+query=select id, product_reference_id from kamille.ek_product',
        'order' => 'input',
    ],
    'ek_comment' => [
        'id' => 'auto_increment',
        'user_id' => 'selectForeignKey+query=select id, email from kamille.ek_user',
        'shop_id' => 'selectForeignKey+query=select id, label from kamille.ek_shop',
        'text' => 'textarea',
        'date_creation' => 'datetime',
        'active' => 'switch',
    ],
    'ek_condition' => [
        'id' => 'auto_increment',
        'type' => 'input',
        'combinator' => 'input',
        'negation' => 'switch',
        'start_group' => 'switch',
        'end_group' => 'switch',
        'left_operand' => 'input',
        'operator' => 'input',
        'right_operand' => 'input',
        'right_operand2' => 'input',
    ],
    'ek_country' => [
        'id' => 'auto_increment',
        'iso_code' => 'input',
    ],
    'ek_country_lang' => [
        'country_id' => 'selectForeignKey+query=select id, iso_code from kamille.ek_country',
        'lang_id' => 'selectForeignKey+query=select id, label from kamille.ek_lang',
        'label' => 'input',
    ],
    'ek_currency' => [
        'id' => 'auto_increment',
        'iso_code' => 'input',
        'symbol' => 'input',
    ],
    'ek_currency_lang' => [
        'currency_id' => 'selectForeignKey+query=select id, iso_code from kamille.ek_currency',
        'lang_id' => 'selectForeignKey+query=select id, label from kamille.ek_lang',
        'name' => 'input',
    ],
    'ek_currency_shop' => [
        'id' => 'auto_increment',
        'currency_id' => 'selectForeignKey+query=select id, iso_code from kamille.ek_currency',
        'shop_id' => 'selectForeignKey+query=select id, label from kamille.ek_shop',
        'exchange_rate' => 'input',
        'active' => 'switch',
    ],
    'ek_discount_rule' => [
        'id' => 'auto_increment',
        'type' => 'input',
        'shop_id' => 'selectForeignKey+query=select id, label from kamille.ek_shop',
    ],
    'ek_discount_rule_has_action' => [
        'discount_rule_id' => 'selectForeignKey+query=select id, type from kamille.ek_discount_rule',
        'action_id' => 'selectForeignKey+query=select id, source from kamille.ek_action',
    ],
    'ek_discount_rule_has_condition' => [
        'discount_rule_id' => 'selectForeignKey+query=select id, type from kamille.ek_discount_rule',
        'condition_id' => 'selectForeignKey+query=select id, type from kamille.ek_condition',
    ],
    'ek_discount_rule_lang' => [
        'discount_rule_id' => 'selectForeignKey+query=select id, type from kamille.ek_discount_rule',
        'label' => 'input',
        'lang_id' => 'selectForeignKey+query=select id, label from kamille.ek_lang',
    ],
    'ek_feature' => [
        'id' => 'auto_increment',
        'label' => 'input',
        'lang_id' => 'selectForeignKey+query=select id, label from kamille.ek_lang',
    ],
    'ek_feature_value' => [
        'id' => 'auto_increment',
        'value' => 'input',
        'lang_id' => 'selectForeignKey+query=select id, label from kamille.ek_lang',
    ],
    'ek_lang' => [
        'id' => 'auto_increment',
        'label' => 'input',
        'iso_code' => 'input',
    ],
    'ek_order' => [
        'id' => 'auto_increment',
        'user_id' => 'selectForeignKey+query=select id, email from kamille.ek_user',
        'reference' => 'input',
        'date' => 'datetime',
        'tracking_number' => 'input',
        'user_info' => 'textarea',
        'shop_info' => 'textarea',
        'shipping_address' => 'textarea',
        'billing_address' => 'textarea',
        'order_details' => 'textarea',
    ],
    'ek_order_has_order_status' => [
        'order_id' => 'selectForeignKey+query=select id, reference from kamille.ek_order',
        'order_status_id' => 'selectForeignKey+query=select id, label from kamille.ek_order_status',
        'date' => 'datetime',
    ],
    'ek_order_status' => [
        'id' => 'auto_increment',
        'label' => 'input',
        'lang_id' => 'selectForeignKey+query=select id, label from kamille.ek_lang',
    ],
    'ek_order_status_shop' => [
        'order_status_id' => 'selectForeignKey+query=select id, label from kamille.ek_order_status',
        'shop_id' => 'selectForeignKey+query=select id, label from kamille.ek_shop',
        'color' => 'color',
    ],
    'ek_payment_method' => [
        'id' => 'auto_increment',
        'label' => 'input',
        'lang_id' => 'selectForeignKey+query=select id, label from kamille.ek_lang',
    ],
    'ek_payment_method_shop' => [
        'payment_method_id' => 'selectForeignKey+query=select id, label from kamille.ek_payment_method',
        'shop_id' => 'selectForeignKey+query=select id, label from kamille.ek_shop',
        'active' => 'switch',
    ],
    'ek_product' => [
        'id' => 'auto_increment',
        'product_reference_id' => 'selectForeignKey+query=select id, natural_reference from kamille.ek_product_reference+firstOptionLabel=Please choose an option+firstOptionValue=0',
    ],
    'ek_product_attibute_value' => [
        'id' => 'auto_increment',
        'label' => 'input',
        'lang_id' => 'selectForeignKey+query=select id, label from kamille.ek_lang',
    ],
    'ek_product_attribute' => [
        'id' => 'auto_increment',
        'label' => 'input',
        'lang_id' => 'selectForeignKey+query=select id, label from kamille.ek_lang',
    ],
    'ek_product_has_comment' => [
        'product_id' => 'selectForeignKey+query=select id, product_reference_id from kamille.ek_product',
        'comment_id' => 'selectForeignKey+query=select id, active from kamille.ek_comment',
    ],
    'ek_product_has_feature' => [
        'product_id' => 'selectForeignKey+query=select id, product_reference_id from kamille.ek_product',
        'feature_id' => 'selectForeignKey+query=select id, label from kamille.ek_feature',
        'feature_value_id' => 'selectForeignKey+query=select id, value from kamille.ek_feature_value',
    ],
    'ek_product_has_product_attribute' => [
        'product_id' => 'selectForeignKey+query=select id, product_reference_id from kamille.ek_product',
        'product_attribute_id' => 'selectForeignKey+query=select id, label from kamille.ek_product_attribute',
        'product_attibute_value_id' => 'selectForeignKey+query=select id, label from kamille.ek_product_attibute_value',
    ],
    'ek_product_has_product_origin' => [
        'product_id' => 'selectForeignKey+query=select id, product_reference_id from kamille.ek_product',
        'product_origin_id' => 'selectForeignKey+query=select id, type from kamille.ek_product_origin',
    ],
    'ek_product_has_tag' => [
        'product_id' => 'selectForeignKey+query=select id, product_reference_id from kamille.ek_product',
        'tag_id' => 'selectForeignKey+query=select id, label from kamille.ek_tag',
        'shop_id' => 'selectForeignKey+query=select id, label from kamille.ek_shop',
    ],
    'ek_product_has_tax_rule' => [
        'product_id' => 'selectForeignKey+query=select id, product_reference_id from kamille.ek_product',
        'tax_rule_id' => 'selectForeignKey+query=select id, condition from kamille.ek_tax_rule',
    ],
    'ek_product_has_video' => [
        'product_id' => 'selectForeignKey+query=select id, product_reference_id from kamille.ek_product',
        'video_id' => 'selectForeignKey+query=select id, uri from kamille.ek_video',
    ],
    'ek_product_lang' => [
        'product_id' => 'selectForeignKey+query=select id, product_reference_id from kamille.ek_product',
        'label' => 'input',
        'description' => 'textarea',
        'url' => 'input',
        'shop_id' => 'selectForeignKey+query=select id, label from kamille.ek_shop',
    ],
    'ek_product_origin' => [
        'id' => 'auto_increment',
        'type' => 'input',
        'value' => 'input',
        'image' => 'upload+profileId=Ekom/kamille.ek_product_origin.image',
    ],
    'ek_product_reference' => [
        'id' => 'auto_increment',
        'natural_reference' => 'input',
        'reference' => 'input',
        'weight' => 'input',
    ],
    'ek_product_reference_shop' => [
        'id' => 'auto_increment',
        'image' => 'upload+profileId=Ekom/kamille.ek_product_reference_shop.image',
        'prix_ht' => 'input',
        'shop_id' => 'selectForeignKey+query=select id, label from kamille.ek_shop',
        'product_reference_id' => 'selectForeignKey+query=select id, natural_reference from kamille.ek_product_reference',
    ],
    'ek_product_reference_shop_lang' => [
        'product_reference_shop_id' => 'selectForeignKey+query=select id, image from kamille.ek_product_reference_shop',
        'label' => 'input',
        'description' => 'textarea',
    ],
    'ek_product_reference_store' => [
        'id' => 'auto_increment',
        'store_id' => 'selectForeignKey+query=select id, label from kamille.ek_store',
        'quantity' => 'input',
        'product_reference_id' => 'selectForeignKey+query=select id, natural_reference from kamille.ek_product_reference',
    ],
    'ek_role_badge' => [
        'id' => 'auto_increment',
        'label' => 'input',
    ],
    'ek_role_group' => [
        'id' => 'auto_increment',
        'label' => 'input',
        'role_group_id' => 'selectForeignKey+query=select id, label from kamille.ek_role_group+firstOptionLabel=Please choose an option+firstOptionValue=0',
    ],
    'ek_role_group_has_role_badge' => [
        'role_group_id' => 'selectForeignKey+query=select id, label from kamille.ek_role_group',
        'role_badge_id' => 'selectForeignKey+query=select id, label from kamille.ek_role_badge',
    ],
    'ek_role_profile' => [
        'id' => 'auto_increment',
        'label' => 'input',
        'backoffice_user_id' => 'selectForeignKey+query=select id, email from kamille.ek_backoffice_user',
    ],
    'ek_role_profile_has_role_badge' => [
        'role_profile_id' => 'selectForeignKey+query=select id, label from kamille.ek_role_profile',
        'role_badge_id' => 'selectForeignKey+query=select id, label from kamille.ek_role_badge',
    ],
    'ek_role_profile_has_role_group' => [
        'role_profile_id' => 'selectForeignKey+query=select id, label from kamille.ek_role_profile',
        'role_group_id' => 'selectForeignKey+query=select id, label from kamille.ek_role_group',
    ],
    'ek_shop' => [
        'id' => 'auto_increment',
        'label' => 'input',
        'lang_id' => 'selectForeignKey+query=select id, label from kamille.ek_lang+firstOptionLabel=Please choose an option+firstOptionValue=0',
        'currency_id' => 'selectForeignKey+query=select id, iso_code from kamille.ek_currency+firstOptionLabel=Please choose an option+firstOptionValue=0',
        'timezone_id' => 'selectForeignKey+query=select id, name from kamille.ek_timezone+firstOptionLabel=Please choose an option+firstOptionValue=0',
    ],
    'ek_shop_configuration' => [
        'shop_id' => 'selectForeignKey+query=select id, label from kamille.ek_shop',
        'key' => 'input',
        'value' => 'input',
    ],
    'ek_shop_has_product' => [
        'shop_id' => 'selectForeignKey+query=select id, label from kamille.ek_shop',
        'product_id' => 'selectForeignKey+query=select id, product_reference_id from kamille.ek_product',
        'active' => 'switch',
    ],
    'ek_shop_has_store' => [
        'shop_id' => 'selectForeignKey+query=select id, label from kamille.ek_shop',
        'store_id' => 'selectForeignKey+query=select id, label from kamille.ek_store',
    ],
    'ek_state' => [
        'id' => 'auto_increment',
        'iso_code' => 'input',
        'label' => 'input',
        'country_id' => 'selectForeignKey+query=select id, iso_code from kamille.ek_country',
    ],
    'ek_store' => [
        'id' => 'auto_increment',
        'label' => 'input',
    ],
    'ek_tag' => [
        'id' => 'auto_increment',
        'label' => 'input',
        'lang_id' => 'selectForeignKey+query=select id, label from kamille.ek_lang',
    ],
    'ek_tax' => [
        'id' => 'auto_increment',
        'reduction' => 'input',
        'tax_lang_id' => 'selectForeignKey+query=select id, label from kamille.ek_tax_lang',
    ],
    'ek_tax_lang' => [
        'id' => 'auto_increment',
        'label' => 'input',
        'lang_id' => 'selectForeignKey+query=select id, label from kamille.ek_lang',
    ],
    'ek_tax_rule' => [
        'id' => 'auto_increment',
        'tax_id' => 'selectForeignKey+query=select id, reduction from kamille.ek_tax',
        'condition' => 'input',
    ],
    'ek_timezone' => [
        'id' => 'auto_increment',
        'name' => 'input',
    ],
    'ek_user' => [
        'id' => 'auto_increment',
        'user_group_id' => 'selectForeignKey+query=select id, label from kamille.ek_user_group',
        'email' => 'input',
        'pass' => 'pass',
        'base_shop_id' => 'input',
        'date_creation' => 'datetime',
        'active' => 'switch',
        'main_address_id' => 'selectForeignKey+query=select id, type from kamille.ek_address',
        'mobile' => 'input',
        'phone' => 'input',
        'pro' => 'switch',
    ],
    'ek_user_group' => [
        'id' => 'input',
        'label' => 'input',
    ],
    'ek_video' => [
        'id' => 'auto_increment',
        'uri' => 'input',
    ],
];
