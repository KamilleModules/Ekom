Faq
==========
2018-03-06




Comment savoir si on est dans le backoffice ou pas ?
----------------------------

```php

E::isBackOffice(); // proposé par le module Ekom
A::isBackOffice(); // proposé par le framework kamille


```




Comment récupérer les variables de contexte ?
--------------------------------


Depuis le back office:

```php
EkomNullosUser::getEkomValue("shop_id");
EkomNullosUser::getEkomValue("lang_id");
EkomNullosUser::getEkomValue("currency_id");
EkomNullosUser::getEkomValue("shop_host");
EkomNullosUser::getEkomValue("currency_iso_code");
EkomNullosUser::getEkomValue("lang_iso_code");


$shopId = E::getShopId($shopId); // utilisez cette variation si vous n'êtes pas sûr(e) de l'origine de $shopId

```


Depuis le front office:

```php
E::getShopId();
E::getLangId();
E::getLangIso();
E::getCurrencyId();
E::getCurrencyIso();
```