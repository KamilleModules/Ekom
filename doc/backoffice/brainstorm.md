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


Context vars are stored in (php) session along with the NullosUser, under the ekom namespace.

```php
$shopId = $_SESSION['nullos']['user']['ekom']['shop_id'];
```


When those 3 vars are defined (shop_id, lang_id, currency_id), then only the backoffice unlocks,
and the admin can use it to administer his/her shop.


To create a shop item, we need to have a currency and a lang.
Therefore, the currency and the lang will be prompted by the ekom application prior to any other object.
Then the shop.





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
 



