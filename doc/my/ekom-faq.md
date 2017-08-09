Ekom faq
===============
2017-08-04




How do I know if the current user should benefit b2b or b2c prices?
---------------------
```php
E::isB2b()
```



Guidelines for developing models?
---------------------
- createBy method always return an instance with sensible values.
If erroneous (asking for an unexisting record for intance),
use XLog:error, then return an instance with default values.

