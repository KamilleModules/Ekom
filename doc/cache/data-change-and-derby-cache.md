Data Change 
======================
2017-11-25




DataChange is a dispatcher that we (ekom devs) should call when the data in the database is updated.


The main idea is to provide an opportunity for the DerbyCache to delete some cache parts upon certain actions.
In particular, data related to the user will benefit this system: the user information access methods benefit 
cache as any other method accessing the database, but the user can update her/his data at any 
moment (i.e. cron is not adapted). 
And so this DataChange allows ekom to delete the cache in real time, while the user updates her/his data.



See document here:
https://github.com/KamilleModules/Ekom/tree/master/doc/ekom-schemas/data-change-with-derby-cache.pdf