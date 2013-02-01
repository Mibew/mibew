/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

(function(Mibew, Backbone, Handlebars) {

    /**
     * @class Represents status panel view.
     */
    Mibew.Views.StatusPanel = Backbone.Marionette.ItemView.extend(
        /** @lends Mibew.Views.StatusPanel.prototype */
        {
            /**
             * Template function
             * @type Function
             */
            template: Handlebars.templates.status_panel,

            /**
             * Map model events to the view methods
             * @type Object
             */
            modelEvents: {
                'change': 'render'
            },

            /**
             * Shortcuts for ui elements
             * @type Object
             */
            ui: {
                changeStatus: '#change-status'
            },

            /**
             * Map ui events to view methods
             * @type Object
             */
            events: {
                'click #change-status': 'changeAgentStatus'
            },

            /**
             * View initializer
             */
            initialize: function() {
                Mibew.Objects.Models.agent.on('change', this.render, this);
            },

            /**
             * Changes users status
             */
            changeAgentStatus: function() {
                this.model.changeAgentStatus();
            },

            /**
             * Override Backbone.Marionette.ItemView.serializeData to pass some
             * extra fields to template.
             * @returns {Object} Template data
             */
            serializeData: function() {
                var data = this.model.toJSON();
                data.agent = Mibew.Objects.Models.agent.toJSON();
                return data;
            }
        }
    );

})(Mibew, Backbone, Handlebars);