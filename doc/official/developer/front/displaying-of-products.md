Affichage de produits
========================
2018-04-25



Ekom propose différentes classes permettant d'afficher des produits.


Pour afficher un produit, on utilisera la classe appropriée (ou une autre de son choix):



Affichage d'une page produit
-------------- 
- Classe: `class-modules/Ekom/Api/Layer/ProductBoxLayer.php`.
- Template: pour l'instant Ekom ne fournit pas de modèle standard, 
mais pour ceux qui utilisent le thème **lee** on pourra chercher dans ce dossier: `theme/lee/widgets/Ekom/Product/ProductBox`



Exemple de page produit:

<img src="image/ekom-product-page.png" alt="Drawing" />



Affichage d'un carousel 
-------------- 
Le carousel est une liste de produits sans tri, filtre ou pagination.


- Classe: `class-modules/Ekom/Api/Layer/MiniProductBoxLayer.php`
- Template (thème **lee**): `theme/lee/widgets/Ekom/CarouselProducts/default`


       
        
```php

// model
//...
$lastVisited = MiniProductBoxLayer::getLastVisitedBoxes($userId);


// controller
$claws
    ->setWidget('maincontent.recentProducts', ClawsWidget::create()
        ->setTemplate("Ekom/CarouselProducts/default")
        ->setConf([
            "title" => "DERNIERS PRODUITS CONSULTÉS",
            "products" => $lastVisited,
        ])
    );
```        

Exemple de carousel:

<img src="image/ekom-carousel.png" alt="Drawing" />


Affichage d'une liste de produits 
-------------- 

Une liste de produits avec tri, filtre (en option) et pagination.


- Classe: `class-modules/Ekom/SqlQueryWrapper/EkomProductListSqlQueryWrapper.php`
- Helper: `class-modules/Ekom/Helper/SqlQueryHelper.php` (génère les requêtes standard de Ekom)
- Template (thème **lee**): `theme/lee/widgets/Ekom/ProductList/ProductCardList/product-list`


        
        
```php


// model
$limit = 60;
$sqlQuery = SqlQueryHelper::getLastVisitedSqlQuery($userId, $limit);
$wrapper = EkomProductListSqlQueryWrapper::create();

/**
 * Personnalisation du plugin sort (facultatif)
 *
 * @var $sortPlugin SqlQueryWrapperSortPlugin
 */
$sortPlugin= $wrapper->getPlugin("sort");
$sortPlugin->setDefaultSort("date_desc");
$sortPlugin->prependSortItems([
    "date_asc" => "Date d'ajout ascendante",
    "date_desc" => "Date d'ajout descendante",
]);


$wrapper->setSqlQuery($sqlQuery)->prepare();
 
$model = [
    // 'title' => "", // optionnel
    'listWrapper' => $wrapper,
];


// controller
$this->getClaws()
    ->setLayout("sandwich_2c/product-list")
    ->setWidget("maincontent.productList", ClawsWidget::create()
        ->setTemplate("Ekom/ProductList/ProductCardList/product-list")
        ->setConf($model)
    );
```        

Exemple de résultat liste avec filtre:

<img src="image/ekom-list-with-filter.png" alt="Drawing" />

Exemple de résultat liste sans filtre:

<img src="image/ekom-list-without-filter.png" alt="Drawing" />