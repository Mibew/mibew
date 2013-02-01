/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

(function(Mibew, Backbone, Handlebars, _) {

    /**
     * @class Represents visitors list
     */
    Mibew.Views.VisitorsCollection = Backbone.Marionette.CompositeView.extend(
        /** @lends Mibew.Views.VisitorsCollection.prototype */
        {
            template: Handlebars.templates.visitors_collection,

            /**
             * Default item view constructor.
             * @type Function
             */
            itemView: Mibew.Views.Visitor,

            /**
             * DOM element for collection items
             * @type String
             */
            itemViewContainer: '#visitors-container',

            /**
             * Empty view constructor.
             * @type Function
             */
            emptyView: Mibew.Views.NoVisitors,

            /**
             * Class name for view's DOM element
             * @type String
             */
            className: 'visitors-collection',

            /**
             * Map collection events to the view methods
             * @type Object
             */
            collectionEvents: {
                'sort': 'renderCollection'
            },

            /**
             * Pass some options to item view
             * @returns {Object} Options object
             */
            itemViewOptions: function(model) {
                var page = Mibew.Objects.Models.page;
                return {
                    tagName: page.get('visitorTag'),
                    collection: model.get('controls')
                }
            },

            /**
             * View initializer.
             * @todo Do something with timer. Do not render whole view!
             */
            initialize: function() {
                // Rerender view to keep timers in items views working
                window.setInterval(_.bind(this.renderCollection, this), 2 * 1000);
                // Register events
                this.on('itemview:before:render', this.updateStyles, this);
            }
        }
    );

})(Mibew, Backbone, Handlebars, _);