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
             */
            initialize: function() {
                // Update time in timers
                window.setInterval(_.bind(this.updateTimers, this), 2 * 1000);
                // Register events
                this.on('render', this.updateTimers, this);
            },

            /**
             * Updates time in timers
             */
            updateTimers: function() {
                Mibew.Utils.updateTimers(this.$el, '.timesince');
            }
        }
    );

})(Mibew, Backbone, Handlebars, _);