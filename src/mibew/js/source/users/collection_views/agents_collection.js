/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */

(function(Mibew) {

    /**
     * @class Represents online agents bar
     */
    Mibew.Views.AgentsCollection = Mibew.Views.CollectionBase.extend(
        /** @lends Mibew.Views.AgentsCollection.prototype */
        {
            /**
             * Default item view constructor.
             * @type Function
             */
            itemView: Mibew.Views.Agent,

            /**
             * Class name for view's DOM element
             * @type String
             */
            className: 'agents-collection',

            /**
             * Map collection events to the view methods
             * @type Object
             */
            collectionEvents: {
                'sort add remove reset': 'render'
            },

            /**
             * View initializer
             */
            initialize: function() {
                // Register events
                this.on('itemview:before:render', this.updateIndexes, this);
            },

            /**
             * Update 'isModelFirst' and 'isModelLast' child views fields on
             * collection 'sort', 'add', 'remove' and 'reset' events.Indexies
             */
            updateIndexes: function(childView) {
                // Create some shortcuts
                var collection = this.collection;
                var model = childView.model;

                if (model) {
                    // Update isModelFirst and isModelLast properties
                    childView.isModelFirst = (collection.indexOf(model) == 0);
                    childView.isModelLast = (
                        collection.indexOf(model) == (collection.length - 1)
                    );
                }
            }

        }
    );

})(Mibew);