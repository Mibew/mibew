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
     * @class Represents default message view
     */
    Mibew.Views.Message = Backbone.Marionette.ItemView.extend(
        /** @lends Mibew.Views.Message.prototype */
        {
            /**
             * Template function
             * @type Function
             */
            template: Handlebars.templates['message'],

            /**
             * Class name for view's DOM element
             * @type String
             */
            className: 'message',

            /**
             * Map model events to the view methods
             * @type Object
             */
            modelEvents: {
                'change': 'render'
            },

            /**
             * Override Backbone.Marionette.ItemView.serializeData to pass some
             * extra fields to template.
             * @returns {Object} Template data
             */
            serializeData: function() {
                var msg = this.model.toJSON();
                var messageKind = this.model.get('kind');

                // Add message fields
                msg.kindName = this.kindToString(messageKind);

                return msg;
            },

            /**
             * Map message kide code to kind name
             * @param {Number} kind Kind code
             * @returns {String} Kind name
             */
            kindToString: function(kind) {
                if (kind == this.model.KIND_USER) {
                    return "user";
                }
                if (kind == this.model.KIND_AGENT) {
                    return "agent";
                }
                if (kind == this.model.KIND_FOR_AGENT) {
                    return "hidden";
                }
                if (kind == this.model.KIND_INFO) {
                    return "info";
                }
                if (kind == this.model.KIND_CONN) {
                    return "connection";
                }
                if (kind == this.model.KIND_EVENTS) {
                    return "event";
                }
                if (kind == this.model.KIND_PLUGIN) {
                    return "plugin";
                }
                return "";
            }
        }
    );

})(Mibew, Backbone, Handlebars);