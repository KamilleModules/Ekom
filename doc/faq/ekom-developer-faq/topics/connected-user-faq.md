Connected User Faq
===================
2017-11-08




1- How do I get information about the connected user?
=====================================================

```php

ConnexionLayer::getUserConnexionData(); // return all the user connexion data
ConnexionLayer::getUserConnexionData(key, default); // return one user connexion data in particular

```



When the user logs in, the ConnexionLayer::getConnexionDataByUserId method is called, receiving the userId as parameter.
This method adds the primary connexion data:

- id
- userBrowserCountry
- userShippingCountry
- userGroupNames


Then the Ekom_Connexion_decorateUserConnexionData hook is called to let modules decorate this data.
