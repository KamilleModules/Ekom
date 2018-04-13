ProductBoxContext
===================
2018-04-13




Le `ProductBoxContext` est l'ensemble des conditions qui permettent de définir les informations 
monétaires (prix, taxe) d'un produit.

Ce contexte dépend du client, c'est pourquoi `ProductBoxContext` est en général une notion qu'on ne retrouve
que sur le front.

Le `ProductBoxContext` n'est défini qu'une fois par process (singleton), au moment où Ekom est initialisé.


Le hash de ce `ProductBoxContext` (`ProductBoxContextHash`) est une valeur intéressante pour les listes,
car elle nous sert à construire des identifiants de cache.


Les 2 éléments `ProductBoxContext` et `ProductBoxContextHash` sont accessibles directement via `E`.



(Experimental)
-------
Il est composé des variables suivantes:

- user_group_name (utilisé par le système de prix par groupe de client ek_product_variation)
- user_group_id (utilisé par tax et discount systems)
- date (mysql date, utilisé par tax et discount systems)
- tax.extra1 
- tax.extra2 
- tax.extra3 
- tax.extra4 
- discount.extra1 



