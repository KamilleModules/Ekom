Caching
==============
2017-05-26



Ekom makes heavy use of the Tabatha caching system.


The strategy used by ekom is basically the [TabathaDb strategy](https://github.com/lingtalfi/TabathaCache#tabatha-db),
with the addition of the following deleteIdentifiers:

- ekomApi.image.product: triggered (by the ekom api) when a product image is created/updated/deleted
- ekomApi.image.productCard: triggered (by the ekom api) when a product card image is created/updated/deleted
