Database calls
=====================
2018-01-30



Ideally, insert or update statements should be using
the same wrapper, so that we can implement a consistent application logic based on hooks.

The main tool that we use to interact with the database is QuickPdo.
The QuickPdoInitializer object allows us to hook into any QuickPdo operations on a per module basis.
This is the wrapper that we are going to use.


In particular, this allows us to implement the sanity check service, see SanityCheckController.