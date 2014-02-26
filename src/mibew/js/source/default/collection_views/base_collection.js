/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */

(function(Mibew, Backbone, _){

    /**
     * Return special contructor for an item view if it exists or the
     * default constructor otherwise. Use in Mibew.Views.CollectionBase and
     * Mibew.Views.CompositeBase
     * @private
     * @param {Backbone.Model} item Collection item
     * @param {Function} ItemViewType Default item view constructor
     * @param {Object} itemViewOptions Additional item view options
     * @returns Item view instance
     */
    var buildItemView = function(item, ItemViewType, itemViewOptions) {
        // Build options object
        var options = _.extend({model: item}, itemViewOptions);
        // Try to find special view for this model
        if (typeof item.getModelType != 'function') {
            return new ItemViewType(options);
        }
        var modelType = item.getModelType();
        if (modelType && Mibew.Views[modelType]) {
            return new Mibew.Views[modelType](options);
        } else {
            return new ItemViewType(options);
        }
    }

    /**
     * @class Represents base collection view
     */
    Mibew.Views.CollectionBase = Backbone.Marionette.CollectionView.extend(
        /** @lends Mibew.Views.CollectionBase.prototype */
        {
            /**
             * Default item view constructor.
             * @type Function
             */
            itemView: Backbone.Marionette.ItemView,

            /**
             * Return special contructor for an item view if it exists or the
             * default constructor otherwise.
             */
            buildItemView: buildItemView
        }
    );

    Mibew.Views.CompositeBase = Backbone.Marionette.CompositeView.extend(
        /** @lends Mibew.Views.CompositeBase.prototype */
        {
            /**
             * Return special contructor for an item view if it exists or the
             * default constructor otherwise.
             */
            buildItemView: buildItemView
        }
    );

})(Mibew, Backbone, _);