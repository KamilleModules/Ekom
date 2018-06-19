Faq
==========
2018-03-06




Comment savoir si on est dans le backoffice ou pas ?
----------------------------

```php

E::isBackOffice(); // proposé par le module Ekom
A::isBackOffice(); // proposé par le framework kamille


```



Connaître la langue utilisée actuellement sur le front
-------------------------------

```php
E::langIsoCode();
```


Connaître la devise utilisée actuellement sur le front
-------------------------------

```php
E::currencyIsoCode();
```


Se connecter, se déconnecter
----------------------------

```php
ConnexionLayer::connectUserById($userId);
ConnexionLayer::disconnectUser();
```
