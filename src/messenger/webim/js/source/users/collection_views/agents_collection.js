/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
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