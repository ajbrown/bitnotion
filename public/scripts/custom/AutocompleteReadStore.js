dojo.provide("custom.AutocompleteReadStore");
dojo.require("dojox.data.QueryReadStore");
dojo.declare(
    "custom.AutocompleteReadStore",
    dojox.data.QueryReadStore,
    {
        fetch: function(request) {
            request.serverQuery = {q: request.query.name};
            return this.inherited("fetch", arguments);
        }
    }
);
var autocompleter;