Ekom connected user data
===============
2017-11-03



When the user connects in the ekom front, there is only one place that defines which data are created:

```php
ConnexionLayer::getConnexionDataByUserId 
```

The above method creates the following data:

- id
- userGroupNames: array of id => group name


And provides the opportunity for modules to decorate this array using the following hook:

```php
Ekom_Connexion_decorateConnexionData ( array &connexionData )
```



Accessing the connexion data
---------------------------

Those connexion data are stored in the session and can be accessed
with the following methods:

```php

E::getUserId( $default=false ) // returns the user id, or throws an exception, or returns a default value
E::userIsConnected() // bool
E::getUserData( $key, $default=null ) // mixed


```
