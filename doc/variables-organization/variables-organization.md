Variables organization
=============================
2017-11-12


Note: 
Kamille lacked a clear/understandable variables organization model.
In the document below I try to distinguish between different types of variables.
I hope it inspires kamille authors to update their doc.





Like with any software, knowing what variables are available to us and how to access them is a crucial bit 
of information.


In this document, I will consider that a php application has three types of variables:


- environment variables 
- contextual variables 
- persistent variables 


Environment variables
=======================

Those are the variables depending from the environment.
They are inferred by the configuration of our application and modules.


In kamille, we distinguish between two levels: application and modules.


### Application configuration variables


Locations: 
- config/application-parameters.php
- ?config/application-parameters-dev.php
- ?config/application-parameters-prod.php


```txt
//////////////////////////////// ApplicationParameters (Kamille)
- app_dir: path
- debug: bool
- theme: lee
```

### Module configuration variables

This is module configuration level.

Location:
- config/modules/Ekom.conf.php
- config/modules/Ekom: (directory)

Use the XConfig (Kamille) object to access module specific configuration.
Also, use E::conf to access the shop specific configuration of ekom.

```txt

//////////////////////////////// XConfig::get(Ekom.*)
- uriProductImageBaseDir: /img/products
- createAccountNeedValidation: bool 
- commentNeedValidation: bool 
- commentModeratorEmail: johndoe@gmail.com 
- passwordRecoveryNbSeconds: 3 * 86400 
- OnTheFlyFormValidatorMessageClass: null 
- nipp.category: 20 
- googleMapKey: 4650efzfiijefizjofoj

//////////////////////////////// E::conf
- acceptOutOfStockOrders: false
- sessionTimeout: 3000
- checkoutMode: singleAddress
- statusProvider: lee
- attribute2TemplateAdaptor: Module\Ekom\Laws\DynamicWidgetBinder\Attribute2TemplateAdaptor\Attribute2TemplateAdaptor
- countryIso: FR
```
 




Contextual variables
=======================

Contextual variables are those immediately accessible by (most of) the objects of the application.

The kamille framework provides us with the ApplicationRegistry object.
We use it to promote variables to a state where they get immediately accessible to all modules.
 

```txt
//////////////////////////////// X::Core_RoutsyRouter
- core.routsyRouteId

//////////////////////////////// EkomApi
- ekom.host: string
- ekom.shop_id: number
- ekom.lang_id: number
- ekom.lang_iso: fra
- ekom.currency_id: number
- ekom.currency_iso: EUR
- ekom.currency_rate: 1.000000

//////////////////////////////// ProductBoxEntityUtil
- ekom.pbc: 
----- shop_id:
----- lang_id:
----- currency_id:
----- ThisApp_isPro: bool
----- ThisApp_userShippingArea: FR
----- ThisApp_userOriginArea: FR
```
 
 




 
 





Persistent variables
=======================

Persistent variables (aka Session variables) are stored in php session (hence their name).
It's basically the user context.

All session variables in ekom are stored using the ekom namespace.
The EkomSession object is used.

Even some ekom modules use the EkomSession object (and therefore are encapsulated inside
the ekom namespace).

The main variables are:

```txt

- ekom:
//////////////////////////////// EkomApi
----- front:
--------- lang_id: number   
--------- currency_id: number    

//////////////////////////////// CartLayer
----- cart:
--------- $shopId:
------------- items:        
----------------- quantity: number   
----------------- token: string        
----------------- product_id: number        
----------------- ?details: 
--------------------- major: array of key => value         
--------------------- minor: array of key => value         
------------- coupons:        

 
//////////////////////////////// CurrentCheckoutData
----- currentCheckoutData:
--------- started: bool
--------- carrier: $carrierName
--------- shipping_address: array (addressModel from UserAddressLayer)
--------- billing_address: array (addressModel from UserAddressLayer)
--------- payment_method:
------------- id: number
------------- name: string
------------- configuration: array


//////////////////////////////// EkomFrontController
----- referer: string, absolute uri


//////////////////////////////// WebApplicationHandler && SessionUser
- frontUser:
----- id: number
----- user_connexion_time: number (timestamp)
----- timeout: number (300)

``` 
