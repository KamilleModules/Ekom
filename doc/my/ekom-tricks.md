Ekom tricks
==============
2017-06-07



Here are the tricks that are used in ekom and not documented elsewhere.





redirect non connected to login, and use referer to go back to the current page
===================================================

This is a pattern that we use to temporarily redirect a non connected user to the login page,
and then get her back on the current page after successful authentication.

Basically, this is useful when you need the user to be connected for whatever reasons (for
instance, if you are on the checkout page, the user must be connected so that we can 
access her addresses).


The login controller (LoginController) will listen and clean the ekom session variable: referer (EkomSession::get(referer)).


So, basically, you just need to redirect to the user to the LoginController (Ekom_login route),
and just before you do so, set the ekom session variable referer to the current url value.
That's it.

Upon successful completion, the LoginController will clean that variable and redirect the user
back to you.


Relevant code snippet:

```php
EkomSession::set("referer", UriTool::getWebsiteAbsoluteUrl());
$link = E::link("Ekom_login", [], true);
return RedirectResponse::create($link);
```


