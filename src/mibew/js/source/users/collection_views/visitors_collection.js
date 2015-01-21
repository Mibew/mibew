/*!
 * This file is a part of Mibew Messenger.
 *
 * Copyright 2005-2015 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

(function(Mibew, Handlebars, _) {

    /**
     * @class Represents visitors list
     */
    Mibew.Views.VisitorsCollection = Mibew.Views.CompositeBase.extend(
        /** @lends Mibew.Views.VisitorsCollection.prototype */
        {
            template: Handlebars.templates['users/visitors_collection'],

            /**
             * DOM element for collection items
             * @type String
             */
            childViewContainer: '#visitors-container',

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
             * Returns default child view constructor.
             *
             * The function is used instead of "childView" property to provide
             * an ability to override child view constructor without this class
             * overriding.
             *
             * @param {Backbone.Model} model The model the view created for.
             * @returns {Backbone.Marionette.ItemView}
             */
            getChildView: function(model) {
                return Mibew.Views.Visitor;
            },

            /**
             * Returns empty view constructor.
             *
             * The function is used instead of "emptyView" property to provide
             * an ability to override empty view constructor without this class
             * overriding.
             *
             * @returns {Backbone.Marionette.ItemView}
             */
            getEmptyView: function() {
                return Mibew.Views.NoVisitors;
            },

            /**
             * Pass some options to item view
             * @returns {Object} Options object
             */
            childViewOptions: function(model) {
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
                this.on('render:collection', this.updateTimers, this);
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