/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
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
            template: Handlebars.templates.status,

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