Contextes
====================
2018-04-09




De manière générale, un contexte dans Ekom est un tableau regroupant des informations sur le client,
et qui est utilisé pour déterminer le prix ou la taxe d'un produit.



D'où viennent les contextes?
----------------------------

En effet, dans Ekom, le prix d'un produit dépend de plusieurs choses, dont les taxes et les réductions
qui lui sont appliquées.

Les taxes et réductions dépendent elles-mêmes de plusieurs facteurs (que l'on appelle contexte), tels que
la date, le groupe du client, l'adresse de livraison du client, etc...


Ekom utilise 2 contextes:

- le contexte de taxe
- le contexte de réduction


Le `contexte de taxe` définit les conditions sous lesquelles les taxes d'un produit sont calculées.
Le `contexte de réduction`  définit les conditions sous lesquelles les réductions d'un produit sont calculées.





Que sont les contextes?
----------------------------

Pour répondre à cette demande de prix/taxe dynamique, Ekom utilise le système de contextes.

Chaque contexte est un simple tableau contenant des variables contenant les informations nécessaires
pour calculer la taxe (pour le contexte de taxe) ou la réduction (pour le contexte de réduction).



Développeur
-------------

Pour accéder aux contextes, on utilise les méthodes suivantes:

```php
E::getTaxContext();
E::getDiscountContext();
```


!> Dans le backoffice, les contexte de taxe est vide (ce serait hasardeux d'essayer de créer un contexte par défaut, car
les client peuvent potentiellement opérer dans des contextes radicalement différents).

En particulier, cela signifie que les prix affichées dans certains endroits du backoffice (les endroits
qui utilisent le modèle ProductBoxEntity, prédominant sur le front) ignorent les taxes.

De même, le contexte de réduction ne contient que les informations non liées à l'utilisateur, c'est à dire le date_segment (voir plus bas
pour les explications).





###### Le contexte de taxe

- ?user_group_id: null|int
- ?extra1: null|string
- ?extra2: null|string
- ?extra3: null|string
- ?extra4: null|string


Note: les modules doivent s'accorder entre eux pour décider qui utilisera extra1, qui utilisera extra2, etc...
En général, c'est le module `ThisApp` qui gère toutes les règles extra.


###### Le contexte de réduction


- ?date_segment: null|dateSegment
- ?user_group_id: null|int
- ?extra1: null|string


Note: les modules doivent s'accorder entre eux pour décider qui utilisera extra1.
En général, c'est le module `ThisApp` qui gère toutes les règles extra.

Note2: il est prévu d'ajouter des règles extra au fur et à mesure que le besoin s'en fait ressentir, mais pas avant.

Note3: le dateSegment est une prorpriété expérimentale qui permet d'obtenir la réduction appliquée à un produit.
Elle n'est utile que dans le cadre du cache des ProductBoxEntity.

dateSegment représente l'unité de temps la plus petite avec laquelle nous souhaitons travailler pour les réductions.
Plus dateSegment est granulaire, plus la mise en place du cache est difficile, c'est pourquoi mettre cette unité à un segment
par jour, ou des demi-journées, voire des quarts de journées est possible, mais il n'est pas conseillé d'aller au-delà
(car la lourdeur du cache croît de manière exponentielle au fur et à mesure que ce segment est de plus en plus précis).

Dans Ekom, pour l'instant le dateSegment correspond à la date du jour (1 segment par jour).





