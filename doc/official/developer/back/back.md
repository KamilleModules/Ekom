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
        - extends `class-controllers/Ekom/Back/Pattern/EkomBackSimpleFormController.php` 
        
- Template: `theme/nullosAdmin/widgets/Ekom/Main/FormList/default.tpl.php`
- Renderer du form: `class-modules/NullosAdmin/SokoForm/Renderer/NullosMorphicBootstrapFormRenderer.php`
