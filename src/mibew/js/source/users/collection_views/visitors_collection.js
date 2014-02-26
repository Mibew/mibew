/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */

(function(Mibew, Handlebars, _) {

    /**
     * @class Represents visitors list
     */
    Mibew.Views.VisitorsCollection = Mibew.Views.CompositeBase.extend(
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
                'sort': 'render'
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
                this.on('composite:collection:rendered', this.updateTimers, this);
            },

            /**
             * Updates time in timers
             */
            updateTimers: function() {
                Mibew.Utils.updateTimers(this.$el, '.timesince');
            }
        }
    );

})(Mibew, Handlebars, _);