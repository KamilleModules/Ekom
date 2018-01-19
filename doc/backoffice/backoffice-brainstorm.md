Brainstorm backoffice
========================
2018-01-11




user
========
We use NullosUser from NullosAdmin module.



context vars
=================

 
- shop_id: 
- lang_id: 
- currency_id:

// extra vars available
- shop_host:
- currency_iso_code:
- lang_iso_code:


Those are stored in the nullos user's storage (in the php session, nullos key),
using the ekom key.


Context vars are stored in (php) session along with the NullosUser, under the ekom namespace.


```php
$shopId = $_SESSION['nullos']['user']['ekom']['shop_id'];
```


When those 3 vars are defined (shop_id, lang_id, currency_id), then only the backoffice unlocks,
and the admin can use it to administer his/her shop.


To create a shop item, we need to have a currency and a lang.
Therefore, the currency and the lang will be prompted by the ekom application prior to any other object.
Then the shop.



Quickstart wizard
------------------
2018-01-19


So, if one of the context vars is missing, ekom should trigger the "quickstart wizard".
This wizard will check for the following:


- at least one record in ek_currency  
- at least one record in ek_lang  
- at least one record in ek_shop
- at least one record in ek_shop_has_currency using the chosen shop_id,
                                    which is either the user's chosen shop_id ($_SESSION.nullos.user.ekom.shop_id), 
                                    or the first shop found otherwise
- at least one record in ek_shop_has_lang using the chosen shop_id (see definition above)


For all failing checks, a notification is displayed, so that the user has the opportunity to 
fix those potentially blocking problems.

                
- then check the $_SESSION.nullos.user.ekom data:
    - if shop_id is 0, then the shop_id selector becomes red (to intice the user to select one)
    - same for currency_id 
    - same for lang_id
 
 
By the way, you can/should use 

```php
EkomNullosUser::getEkomValue(shop_id)  
```

to access those context variables.






Tile
=============
2018-01-12


A tile is just a term to represent the combo of a related form and list widgets.
For instance, the currency tile represents the ensemble of the currency form widget on a page,
and the currency list widget on another page.

In other words, the tile is a way to factorize a form/list widget combo in ekom.



Page
==========
2018-01-12


A page in ekom admin (nullos) is the organization unit.
It is used to compose breadcrumbs.
A page name always use the singular form (not plural).

It often (always) has the same structure:
- a page top
- a page content

The page top contains:
- the breadcrumb
- the page title
- the page actions

The page content depends on the uri.



PageTop
===========
2018-01-12

This is a widget contained in almost all pages.


The page top widget contains 3 distinct elements:
- the breadcrumb
- the page title
- the page actions



Breadcrumb item
===================
2018-01-12

A breadcrumb item is used by the breadcrumb contained in the pageTop.
It's just a model with the following structure:

- route
- label
 



