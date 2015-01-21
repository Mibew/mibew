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
     * @class Represents defaut status view
     */
    Mibew.Views.Status = Backbone.Marionette.ItemView.extend(
        /** @lends Mibew.Views.Status.prototype */
        {
            /**
             * Template function
             * @type Function
             */
            template: Handlebars.templates['chat/status/base'],

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