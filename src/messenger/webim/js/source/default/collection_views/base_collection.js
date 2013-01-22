/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

(function(Mibew, Backbone, _){

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
             * @param {Backbone.Model} item Collection item
             * @param {Function} ItemViewType Default item view constructor
             * @param {Object} itemViewOptions Additional item view options
             * @returns Item view instance
             */
            buildItemView: function(item, ItemViewType, itemViewOptions) {
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
        }
    );

})(Mibew, Backbone, _);