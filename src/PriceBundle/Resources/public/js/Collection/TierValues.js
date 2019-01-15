"use strict";
/**
 * Tier value collection
 */
App.register("Collection.TierValues", Backbone.Collection.extend({
    "url": "/admin/price/tiervalue/save",
    "model": App.get("Model.TierValue"),
    /**
     * saves the changed tier value models that have changed
     * @trigger sync
     * @trigger save
     * @trigger error
     */
    "saveChanges": function(){
        var _this = this;
        var formData = []
        _(this.models).each( function(post) {
            if(post.changedAttributes() !== false){
                formData.push( post.toJSON() );
            }
        } );

        $.ajax({
            "url" : this.url,
            "type": "post",
            "data": {data: formData},
            "dataType": "json",
            "success": function(){
                _this.trigger("save", _this);
                _this.trigger("sync", _this);
            },
            "error": function(){
                _this.trigger("error", _this);
            }
        });
    }
}));
