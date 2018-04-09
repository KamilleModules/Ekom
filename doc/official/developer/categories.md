Catégories
======================
2018-04-09




Trouver toutes les catégories filles par rapport à une catégorie donnée
-------------------------

Les 2 méthodes suivantes sont deux variations permettant de renvoyer des informations sur la catégorie courante, 
ainsi que sur toutes les filles.


Pour ne renvoyer que les filles, on utilisera simplement la fonction php `array_shift`.


```php

$catsByName = CategoryCoreLayer::create()->getSelfAndChildren("equipement"); // accès par nom de catégorie
array_shift($catsByName); // maintenant on n'a que les filles

$catsById = CategoryCoreLayer::create()->getSelfAndChildrenByCategoryId(1); // accès par id de catégorie
// ...

```


Si on souhaite n'avoir que les ids (plutôt que toutes les informations), on peut utiliser le raccourci suivant:

```php
$catIds = CategoryLayer::getSelfAndChildrenIdsById(1);
array_shift($catIds); // si on ne veut que les filles...



```


Trouver les ids des cartes pour une catégorie donnée (et toutes les catégories filles)
----------------


```php
$cardIds = CategoryLayer::getCardIdsByCategoryId(1);
```