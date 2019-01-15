"use strict";
/**
 * Carriers collection
 */
App.register("Collection.Carriers", Backbone.Collection.extend({
    model: App.get("Model.Carrier"),
    /**
     * create the url for filtering the data by country
     * @return {string}
     */
    "url": function(){
        if(typeof this.country === "undefined" || this.country === null){
            throw new Error("Please use Collection.TierValues::setCountry first");
        }
        return "/admin/price/tiervalue/list/" + this.country;
    },
    /**
     * sets the country ISO code
     * @param country
     */
    setCountry: function(country){
        this.country = country;
    },
    /**
     * Pre-processes the data from the server
     * @param data
     * @return {Array}
     */
    parse: function(data){
        var carriers = [];
        _.each(data, function(el){
            var tmp = [];
            el.tiers = el.tiers || [];
            _.each(el.tiers, function(tier){
                tmp.push(App.new("Model.TierValue", tier));
            });
            el.tiers = tmp;
            carriers.push(el);
        });
        return carriers;
    }
}));
