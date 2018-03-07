L'administration générée
==================
2018-03-07



L'administration générée est, comme son nom l'indique, générée automatiquement à partir 
de la base de données de Ekom.


Il est possible d'effectuer la plupart des tâches d'administration via cette interface.


Les pages générées sont appelées des pages `liste/formulaire`.

Lorsqu'on sait utiliser une page `liste/formulaire`, on sait utiliser l'administration générée.


!> Chaque page `liste/formulaire` générée correspond à un élément de la base de données appelé **table**. 




Le menu de l'administration générée
-------------------------


Pour accéder à une page `liste/formulaire`, on commence par déplier l'élément de menu "Generated" situé dans le menu gauche,
puis on clique sur le nom de la table (l'élément) pour ouvrir la page `liste/formulaire` correspondante.



<img src="image/back/generated-tables.png" />



La liste
-----------

Lorsqu'on ouvre une page `liste/formulaire`, une liste affichant les éléments existant apparaît.

 
<img src="image/back/list-example.png" />


Chaque liste est comme un tableau constitué de plusieurs colonnes et plusieurs lignes.

Il est possible de:

- trier les résultats par colonne(s)
- filter les résultats par colonne(s)
- modifier le nombre de lignes affichées par page
- supprimer une ligne
- supprimer plusieurs lignes d'un coup (sélectionnez d'abord plusieurs lignes, puis cliquez sur la petite croix au-dessus à droite)
- éditer les informations d'une ligne
- créer un nouvel élément dans ce tableau (bouton d'ajout en haut à droite de la liste)


Le formulaire
-----------------

Le formulaire permet de créer un nouvel élément dans la liste, ou bien de modifier un élément
existant de la liste.

Même lorsque le formulaire s'affiche, la liste des éléments est toujours visible en-dessous.
Cela permet de voir en direct les modifications effectuées.

<img src="image/back/list-form-example.png" />



Le formulaire utilise deux modes de fonctionnement:

- le mode `insert` (ajouter un nouvel élément)
- le mode `update` (modifier un élément existant)



###### Le mode insert

En mode `insert`, on peut valider le formulaire de deux manières:

- soit à l'aide du bouton "Submit"
- soit à l'aide du bouton "Submit and update"


Le bouton "Submit" permet de poster le formulaire et de rester en mode `insert`.
Ce mode est pratique lorsqu'on veut ajouter plusieurs éléments à la suite.


Le bouton "Submit and update" permet de poster le formulaire et de passer directement en mode `update`.
Ce mode est peut-être le plus intuitif, car il permet de corriger le formulaire directement après l'avoir posté,
et également d'utiliser les `liens-pivots` que j'explique un peu plus bas.

En mode insert, les `liens-pivots` sont grisés comme on peut le voir sur le schéma ci-dessus (le lien "Voir les 
user-user groups" est grisé, et on ne peut pas cliquer dessus).


###### Le mode update

En mode `update`, seul le bouton "Submit" est disponible.

Les `liens-pivots` sont disponibles et l'utilisateur peut cliquer dessus.

<img src="image/back/form-update-example.png" />


#### Les liens pivots

Souvent, les éléments sont liés entre eux.
Par exemple, un groupe d'utilisateurs contient des utilisateurs, et donc le groupe et les utilisateurs sont liés.

Les `liens-pivots` permettent d'accéder rapidement aux éléments liés.
Ils facilitent la tâche de l'utilisateur du backoffice.


Les liens pivots sont toujours affichés en dessous du formulaire.


