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
            template: Handlebars.templates['users/status_panel'],

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