Backoffice
======================
2018-03-09




Créer un élément de menu
---------------------

`class-modules/Ekom/Back/Helper/BackHooksHelper.php`

```php
BackHooksHelper::NullosAdmin_layout_sideBarMenuModelObject
```


Créer une page simple
---------------------

[Technique de création de page dans le framework kamille](http://www.ling-docs.ovh/kamille/#/developer/developer-memo?id=cr%c3%a9er-une-page)




Créer une page Morphic formulaire
----------------


- morphic form config: `config/morphic/Ekom/back/utils/cache_manager.form.conf.php`
    - title: le titre du formulaire 
    - description: une description  
    - form: l'instance SokoFormInterface  
    - submitBtnLabel: le label du bouton submit  
    - feed: la fonction à appeler pour pré-remplir le form en mode update (voir `Controller\Ekom\Back\EkomBackController::handleMorphicForm` pour plus de détails)  
    - process: la fonction à appeler lorsque les valeurs du formulaire sont remplies (voir `Controller\Ekom\Back\EkomBackController::handleMorphicForm` pour plus de détails)  
    - ric: les ric pour ce formulaire  
    - formAfterElements: tableau pour ajouter des éléments supplémentaires comme les liens-pivots par exemple (voir exemple dans `config/morphic/Ekom/back/utils/cache_manager.form.conf.php`).  
- Controllers: 
    - `class-controllers/Ekom/Back/Utils/CacheManagerController.php` 
        - extends `class-controllers/Ekom/Back/Pattern/EkomBackSimpleFormListController.php`    
            - verticalLeftMenu: tableau d'items. Chaque item:
                - 0: label
                - 1: lien
                - 2: actif (bool)
        
- Template: `theme/nullosAdmin/widgets/Ekom/Main/FormList/default.tpl.php`
- Renderer du form: `class-modules/NullosAdmin/SokoForm/Renderer/NullosMorphicBootstrapFormRenderer.php`



Créer une page Morphic liste
----------------


- morphic list config: `config/morphic/Ekom/back/catalog/product.list.conf.php`
    - title: le titre de la liste
    - table: une référence de la table. Est utilisée par l'ajax service back.morphic (`service/Ekom/ecp/api.php`)  
    - viewId: l'identifiant de la liste (par exemple: back/catalog/product)  
    - headers: les champs à afficher. Tableau de `column` => label. La dernière colonne spéciale est: `_action => ''` si vous utilisez les actions.   
    - headersVisibility: les colonnes à masquer. `column` => bool  
    - realColumnMap: permet de rectifier les fonctions de tri/recherche. Tableau de `column` => `queryRealCol`, queryRealCol étant le nom tel qu'utilisé dans la requête sql (exemple: pcl.product_card_id)  
    - having: tableau des colonnes qui sont utilisées dans la clause having (plutôt que where). Cela est particulièrement pour le filtrage des données  
    - querySkeleton: la structure de la requête, en remplaçant les colonnes par `%s` (exemple: `select %s from my_table`)  
    - queryCols: les `columns` à intégrer dans le querySkeleton; l'ensemble de la syntaxe mysql est possible (as, concat, if, ...)  
    - context: un ensemble de variables arbitraires passées par le contrôleur. Notez que le service ajax back.morphic les recevra également.
    - deadCols: un tableau de `column` qui n'auront pas de tri ni de filtre (par exemple pour les images) 
    - colSizes: un tableau de `column` => largeur (en pixel) 
    - colTransformers: un tableau de `column` => callback permettant de transformer les colonnes. 
            callback ( columnValue, array row )
    
    - formRoute: la route vers le lien vers le formulaire correspondant généré dans la rowAction par défaut 
    - rowActions: laisser vide pour utiliser les actions par défaut. Un tableau d'action.
        - name: le nom symbolique de l'action (ex: update)             
        - label: le label (exemple: Modifier)             
        - icon: ex fa fa-pencil             
        - link: le lien             
        - ?confirm: le texte de confirmation si c'est une action qui nécessite une confirmation             
        - ?confirmTitle: le titre du dialogue de confirmation             
        - ?confirmOkBtn: le texte de bouton validant la demande de confirmation             
        - ?confirmCancelBtn: le texte de bouton annulant la demande de confirmation             
     
- Controllers: 
    - `class-controllers/Ekom/Back/Catalog/ProductController.php` 
        - extends `class-controllers/Ekom/Back/Pattern/EkomBackSimpleFormListController.php` 
        
- Template: `theme/nullosAdmin/widgets/Ekom/Main/FormList/default.tpl.php`
- Renderer du widget: `class-themes/NullosAdmin/Ekom/Back/GuiAdminTableRenderer/GuiAdminTableWidgetRenderer.php`
- Renderer de la liste: `planets/GuiAdminTable/Renderer/MorphicBootstrap3GuiAdminHtmlTableRenderer.php`


###### Le fichier morphic list config

La variable `$context` existe dans ce fichier.
Elle est passée par le contrôleur via les paramètres de la méthode `Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController->doRenderFormList`.

Le fichier de liste est mergé avec `planets/Kamille/Utils/Morphic/assets/list/_default.list.conf.php` qui fournit les valeurs par défaut.