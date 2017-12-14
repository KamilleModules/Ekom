Ekom connection
===================
2017-12-14



You should always use 

E::isUserConnected and E::getUserId

If you do so, then you have the option to use the following method: 

```php
EkomRootUser::connectAs 
```

in order to temporarily connect as another user.

