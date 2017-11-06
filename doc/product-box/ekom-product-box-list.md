Ekom product box list
=================
2017-11-06



The basic idea for creating a list, from the developer's point of view is the following:


- we start with a request which return cardIds. It doesn't/can't use the **order** clause, something like the following:

```sql
select product_card_id 
from ...
where ...
```

That's because we can't order by sale price, which is arguably a must-have feature for any e-commerce
(see the **database/refuting-the-idea-of-sale-price-in-the-database.md** document for more details).


Then, we convert the card ids into full boxes (product boxes):

```php
//pseudo code
$boxes = convertCardIdsToBoxes ( $cardIds )
```


Then, we need to address the "order" problem.
Our technique, sadly (because of the sale price) is to collect all items with sql and then using plain php
to sort and paginate the results.

It looks like this (pseudo code):

```php

$items = SomePhpHelper::addOrderByAndPagination ( $boxes, MyOrderObject::create, MyPaginationObject::create )
```


Note: in ekom we like to use the ListBundle object (from the ListParams planet) to encapsulate the whole list system, 
since it promotes widget elements re-usability in the view.


        
        
        