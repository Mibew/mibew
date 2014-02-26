/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */

(function(Mibew, Backbone, Handlebars) {

    /**
     * @class Represents defaut status view
     */
    Mibew.Views.Status = Backbone.Marionette.ItemView.extend(
        /** @lends Mibew.Views.Status.prototype */
        {
            /**
             * Template function
             * @type Function
             */
            template: Handlebars.templates.chat_status_base,

            /**
             * Class name for view's DOM element
             * @type String
             */
            className: 'status',

            /**
             * Map model events to the view methods
             * @type Object
             */
            modelEvents: {
                'change': 'render'
            },

            /**
             * Handler of 'before:render' event. Show or hide status.
             */
            onBeforeRender: function() {
                if (this.model.get('visible')) {
                    this.$el.show();
                } else {
                    this.$el.hide();
                }
            }
        }
    );

})(Mibew, Backbone, Handlebars);