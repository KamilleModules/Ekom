(function () {
    if ('undefined' !== typeof stanConfigurableItems) {


        stanConfigurableItems.moduleAddCallback("payment", function (oApi) {


            var jPanel = null;

            var ModuleHandler = function () {

            };
            ModuleHandler.prototype = {
                init: function (jModulePanel) {
                    jPanel = jModulePanel;
                },
                // openPanel: function(jModulePanel){
                //     jModulePanel.show();
                // },
                // closePanel: function(jModulePanel){
                //     jModulePanel.hide();
                // }
                getData: function (jModulePanel) {
                    /**
                     * Normally, you would inspect your panel and extract info from it (I'm just too lazy here)
                     */
                    return {
                        "boris": "vian"
                    };
                }
            };

            oApi.addHandler("MyModule1", new ModuleHandler());
        });


    }
})();
