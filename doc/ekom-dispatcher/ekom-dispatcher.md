Ekom Dispatcher 
======================
2017-11-25 --> 2017-11-30





Ekom Dispatcher dispatches the events of the ekom application.


The primary goal of this dispatcher is to refresh the appropriate application cache.


The following events are dispatched (replace variables with their values):


- newOrder-$userId: a new order was placed by $userId
- user.address-$userId: a new address has been created/updated/deleted





EventNaming convention
==========================
I try to implement a method name convention:


- eventName: <firstPart> (<-> <secondPart>)?
- firstPart: <recommendedForm> | <anyForm>
- recommendedForm: <tableUpdateForm>  
- tableUpdateForm: <namespace> <.> <table>    
- namespace: string, a namespace. Possible namespaces are (work in progress):
                - user (something that belongs to an user)
- table: string, an abstract identifier representing the table.
            In ekom, usually removes the ek_ prefix to get the table.
- anyForm: string
- secondPart: string, the variable part of the event name.
                    Ideally, dot separated variables in a predefined order, 
                    such as the first component is the most important (encapsulates
                    the others).            
            
                        














DEPRECATED
=============
DataChange is a dispatcher that we (ekom devs) should call when the data in the database is updated.


The main idea is to provide an opportunity for the DerbyCache to delete some cache parts upon certain actions.
In particular, data related to the user will benefit this system: the user information access methods benefit 
cache as any other method accessing the database, but the user can update her/his data at any 
moment (i.e. cron is not adapted). 
And so this DataChange allows ekom to delete the cache in real time, while the user updates her/his data.



See document here:

[data-change-with-derby-cache.pdf](https://github.com/KamilleModules/Ekom/tree/master/doc/ekom-schemas/data-change-with-derby-cache.pdf)
