Brainstorm backoffice
========================
2018-01-11




user
========
We use NullosUser from NullosAdmin module.

In the php session, NullosUser resides here:

- $_SESSION
----- nullos
--------- user: 
------------- ekom: (here is the ekom's nullos user data: an array of key/values) 





context vars
=================

 
- shop_id: 
- lang_id: 
- currency_id:

// extra vars available
- shop_host:
- currency_iso_code:
- lang_iso_code:



The context vars represent the default environment the admin user is interacting with.    

The shop should be chosen first.
Then the currency is the display currency: the currency in which prices will be displayed in 
the back office, not the shop.base_currency_id!



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
    - if shop_id is 0, then the shop_id selector becomes red (to entice the user to select one)
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
 



Lists and forms, the main course
=============================
2018-02-05



Most of the pages in the bo need a form and/or list widget.
Although each page is unique, we try to use the following patterns,
that new implementors can browse to have an idea how to get started.


Default pattern
-------------------

In this pattern, the idea is to show a list of items.
There is an "add item" button.
If we click on it, the corresponding form appears (in insert mode), on the top of the list.
 
Also, each line of the list contains an "update" link. Clicking on this "update" link also make
the form appear above the list, but this time in update mode.


### Nested elements
Some forms contain nested list elements.
The main idea is to put more info on the same page, so that the user has more power.
The use cases for nested elements are for instance:

- the list for ek_user_has_address in the ek_user form,
            note that the user and the address already exist, the nested element is the middle (has) table. 
- the list for ek_tax_lang in the ek_tax form



### Implementation details for the default pattern

The formList widget can be in one state amongst the following:

- initial state: the state with only the list, which is the state the user starts with every time
- insert state: the state with the form in insert mode above the list. 
                The user clicked the "insert an item" button to go into that state 
- update state: the state with the form in update mode above the list.
                The user clicked the "update THIS item" button at the end of a table line to go into this state



#### To "display/not display" the form.

IF
    form|ric? form shows up (meaning form or ric in $_GET)
        - form -> insert mode
        - ric -> update mode
        Note: form is provided by the "add new item" button, and ric is actually translated to 
        the real ric fields and is passed via the "update this item" buttons at the end of the table lines.
        
ELSE
    form doesn't show up


#### The list nested element

- don't show up in insert mode, only in update mode 
            That's actually very important to understand why.
            The "nested element list" CAN only appear if at least one record (source record) already exist.
            
- is always inside a form
- correspond to relevant foreign tables.
        For instance:
            - ek_item_lang (parent: ek_item)
            - ek_item_has_address (parent: ek_item)
- defined in the parent form, or in the hosting Controller
- use the morphic-context system, to refresh the (contextual) list via ajax.
        Note that the morphic system is defined in the parent form, or in the hosting Controller
        And the second part of the morphic implementation is written in the config files 
        (ex: tax.form.conf.php, tax.list.conf.php, ...)
- it also contains a button to add a "new related element"
        
        
#### The related element

The related element is yet another page.
It is accessed when the user clicks on the list nested element's "add new item" link or list's "update this item" links.


The main characteristic of this page is that there is a source element id.
In other words, this page requires a source element id to function.

The source element is the element from which the user come (the element to which the related element is related to).

The related element page consists of at least the following elements:

- an "add item" button (adds form to $_GET)
- an "back to source list" button (redirects to the source element list)
- related form: the form always shows up
- related list: the list always shows up

The related form and related lists always depend on the sourceElementId, which must be passed to the formList widget.
Like with the soure element, there is an "update this line" button at the end of each line of the table.
Those links pass the ric via the uri.



Example to recap
=====================

So for instance if you have an user_has_item table.
If you use the "default pattern", then the user alone would be the first form/list widget.
Then, if the user clicks an "update this line" button, the page appears in update mode.

In this "update" mode, it might be the case that a nested list element appear.
If that's the case, clicking either on the "add new item" button (of this nested list element)
or an "update this line" button at the end of the nested list element's table will redirect
to the "related element's" page, either in insert mode ("add new item" button) or in 
update mode ("update this line" button). 









        












 










































