"use strict";
/**
 * View that handles the country changes
 */
App.register("TierValue.Admin.Views.List", Backbone.View.extend({
    /**
     * The root element of the view
     */
    "el": "#tier-values-admin",

    /**
     * Register event listeners
     */
    "events": {
        "change #country-selector": "initTiers",
        "click #save-tiers": "submit"
    },

    /**
     * The view's constructor
     */
    "initialize": function(){
        this.table = this.$("#tier-values");
        this.rowTemplate = _.template($("#row-template").html());
        this.successMessage = null;
    },

    /**
     * Callback for the country change event
     * @param jQuery.Event event
     */
    "initTiers": function(event){
        var country = $(event.target).val(),
            collection = App.new("Collection.Carriers"),
            _this = this;
        this.currency = $(event.target).find(":selected").data("currency");
        this.listenToOnce(collection, "sync", function(){
            _this.saveCollection = App.new("Collection.TierValues");
            _this.table.removeClass("hidden");
            _this.$("#save-tiers").removeClass("hidden");
            _this.showValues(collection);
            _this.removeSuccessMessage();
        });
        this.listenToOnce(collection, "error", this.handleSyncError);

        collection.setCountry(country);
        collection.fetch();
    },

    /**
     * Displays an error if the carrier list cannot be retrieved
     */
    "handleSyncError": function(){
        showModalAlert("Could not retrieve the carrier list");
    },

    /**
     * Creates the table rows for the carriers
     * @param collection
     */
    "showValues": function(collection){
        var _this = this;
        _this.resetRows();
        collection.each(function(value){
            _this.buildListRow(value);
        });
        this.table.find(":input:first").focus();
    },

    /**
     * Removes all the table rows for the carriers
     */
    "resetRows": function(){
        this.table.find("tbody").find("tr").remove();
    },

    /**
     * Builds the html for the carrier row
     * @param model
     */
    "buildListRow": function(model){
        var params = {
            carrierName: model.get("name"),
            currency: this.currency
        };
        var tpl = null;
        var _this = this;
        _.each(App.boot.tiers, function(definedTier){
            var modelTiers = model.get("tiers") || [];
            var modelTier = modelTiers.filter(function(el){
                return el.get("tier_id") === definedTier.id;
            });
            var additionalData = {
                "tier_id": definedTier.id,
                "carrier_id": model.get("id"),
                "billing_provider_id": model.get("provider_id"),
                "currency": _this.currency
            };

            modelTier = modelTier.length === 1 ? modelTier[0].set(additionalData) : App.new("Model.TierValue",additionalData);

            _this.saveCollection.add(modelTier);
            params['value_' + definedTier.id] = modelTier.get("value");
            params['cid_' + definedTier.id] = modelTier.cid;
        });
        tpl = $(this.rowTemplate(params));

        tpl.find(":input").on("change", function(e){
            var el = $(e.target);

            _this.updateModelPrices(el.data("cid"), {
                "value": el.val()
            });
        });

        this.table
            .find("tbody")
            .append(tpl);
    },

    /**
     * Updates the value for a tier value model
     *
     * @param cid
     * @param data
     */
    "updateModelPrices": function(cid, data){
        this.saveCollection.each(function(model){
            if(model.cid === cid){
                model.set("value", data.value);
                return false;
            }
        });
    },

    /**
     * Submits the tier value changes to the server
     */
    "submit": function(){
        var _this = this;
        _this.removeSuccessMessage();
        this.listenToOnce(this.saveCollection, "sync", function(){
            this.stopListening(this.saveCollection);
            _this.enableSubmit();
            _this.successMessage = App.new("Admin.TierValue.View.SuccessMessage");
            _this.$el.prepend(_this.successMessage.render());
        });
        this.listenToOnce(this.saveCollection, "error", function(){
            _this.stopListening(this.saveCollection);
            _this.enableSubmit();
            showModalAlert("We could not save the tier values");
        });
        this.disableSubmit();
        this.saveCollection.saveChanges();
    },

    /**
     * Disables the submit button and displays a loader
     */
    "disableSubmit": function(){
        this.$("#save-tiers").addClass("hidden");
        this.$("#save-holder").append(_.template($("#loader-template").html())());
    },

    /**
     * Removes the loader and changes the visibility of the save button
     */
    "enableSubmit": function(){
        this.$("#save-tiers").removeClass("hidden");
        this.$("#save-holder").find(".loader").remove("");
    },

    /**
     * Removes the success save message
     */
    "removeSuccessMessage": function(){
        if(this.successMessage !== null){
            this.successMessage.remove();
        }
    }
}));
