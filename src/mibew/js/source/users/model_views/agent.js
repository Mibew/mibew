/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */

(function(Mibew, Backbone, Handlebars) {

    /**
     * @class Represents agent view.
     */
    Mibew.Views.Agent = Backbone.Marionette.ItemView.extend(
        /** @lends Mibew.Views.Agent.prototype */
        {
            /**
             * Template function
             * @type Function
             */
            template: Handlebars.templates.agent,

            /**
             * Name of wrapper tag for an agent view
             * @type String
             */
            tagName: 'span',

            /**
             * CSS class name for view's DOM element
             * @type String
             */
            className: 'agent',

            /**
             * Map model events to the view methods
             * @type Object
             */
            modelEvents: {
                'change': 'render'
            },

            /**
             * View initializer
             */
            initialize: function() {
                // Initialize fields and methods of the instance

                /**
                 * Indicates if model related to the view is first in collection
                 * @type Boolean
                 * @fieldOf Mibew.Views.Agent
                 */
                this.isModelFirst = false;

                /**
                 * Indicates if model related to the view is last in collection
                 * @type Boolean
                 * @fieldOf Mibew.Views.Agent
                 */
                this.isModelLast = false;
            },

            /**
             * Override Backbone.Marionette.ItemView.serializeData to pass some
             * extra fields to template. Add 'isFirst' and 'isLast' values.
             * Following additional values available in template:
             *  - 'isFirst': indicates if model is first in collection
             *  - 'isLast': indicates if model is last in collection
             * @returns {Object} Template data
             */
            serializeData: function() {
                var data = this.model.toJSON();
                data.isFirst = this.isModelFirst;
                data.isLast = this.isModelLast;
                return data;
            }
        }
    );

})(Mibew, Backbone, Handlebars);