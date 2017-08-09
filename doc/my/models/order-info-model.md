Order info model
==================
2017-08-07



- user_id: int
- reference: string
- date: datetime
- tracking_number: string
- user_info: 
    select * from ek_user
    - id
    - shop_id
    - email
    - pass
    - pseudo
    - first_name
    - last_name
    - date_creation
    - mobile
    - phone
    - newsletter
    - gender
    - birthday
    - active
- shop_info: 
    - id: (shop)
    - label: (shop)
    - host: (shop)
    - lang_id: (shop)
    - currency_id: (shop)
    - timezone_id: (shop)
    - iso_code: (currency)
    - exchange_rate: (shop_has_currency)
    - timezone: (timezone.name)
    - address: 
        - city
        - postcode
        - address
        - country_id
        - country: label

- shipping_address: <addressModel> | false (false if shipping address doesn't apply: downloadable product)
- billing_address: <addressModel>
- order_details: <orderModel>
    - paymentMethod: creditCard
    without the following keys:
    - checkoutMode
    - billingAddress
    - shippingAddress
    - shippingAddresses
    - selectedShippingAddressId
    - defaultCountry
    - shippingAddressFormModel
    - useSingleCarrier
    - paymentMethodBlocks
    - currentStep
    - paymentMethodId
    - paymentMethodOptions






