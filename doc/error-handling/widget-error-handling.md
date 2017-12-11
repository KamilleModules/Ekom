Widget error handling
========================
2017-11-27



When a widget has an error, its model has the following structure:

- errorCode: string, an identifier representing the error
- errorTitle: string, the title for the error 
- errorMessage: string, the error message 
- ...your own 



We can use ClawsWidgetError object to help us implementing such a system.