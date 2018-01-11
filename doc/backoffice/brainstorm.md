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


Context vars are stored in (php) session under the "ekomback" namespace:

```php
$shopId = $_SESSION['ekomback']['shop_id'];
```



If the shop_id key is not set, then the shop form page should be displayed.
If the lang_id key is not set, then the shop lang page should be displayed.
If the currency_id key is not set, then the currency form page should be displayed.


When those 4 vars are defined (user_id, shop_id, lang_id, currency_id), then only the backoffice unlocks,
and the admin can use it to administer his/her shop.










 



