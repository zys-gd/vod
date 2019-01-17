"use strict";
App.register('TierValue.Module', _.extend(new App.Module, Backbone.Events, {

    /**
     * Constructor
     */
    initialize: function(){

        App.dispatcher.on('ready', this.ready, this);
    },

    /**
     * Method called when the DOM is ready to be used.
     */
    ready: function(){
        var router = App.new("TierValue.Admin.Router");
        this.listenTo(router, "route:index", this.list);
        Backbone.history.start({pushState: true, root: "/admin/price/tiervalue/list"});
        //this.view = App.new('GamesList.view.List');
    },

    /**
     * Callback for the route:index route
     */
    list: function(){
        var view = App.new("TierValue.Admin.Views.List");
    }
}));