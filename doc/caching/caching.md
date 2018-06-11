Caching strategy in Ekom
===========================
2018-06-11




In Ekom, we use the two strategies from TabathaCache2:

- daily cache strategy
- organic strategy



What If you change a route?
------------------

However, also note that some of the Ekom methods include the route links in the cached content,
which means if you are manually changing a route in your app, you'll need to refresh your cache.

The Application module suggests (and Ekom module agrees) that you add the following delete identifier to your cache (when you call the get method):

- ```_route_```





By adding this delete identifier, you'll be able to make a route change, and then delete only the cache entries
which encapsulate a link.

When in doubt, or if a module doesn't follow this Ekom convention, just refresh all your cache.