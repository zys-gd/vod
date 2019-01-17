"use strict";
/**
 * Class that creates the HTML for the success message
 */
App.register("Admin.TierValue.View.SuccessMessage", Backbone.View.extend({
    "click .close" : "remove",

    /**
     * CReates the message html
     * @return {*}
     */
    "render": function(){
        this.setElement($(
            _.template($("#success-message").html())()
        ));
        return this.$el;
    }
}));
