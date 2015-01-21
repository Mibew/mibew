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
     * @class Represents Redirect control view
     */
    Mibew.Views.RedirectControl = Mibew.Views.Control.extend(
        /** @lends Mibew.Views.RedirectControl.prototype */
        {
            /**
             * Template function
             * @type Function
             */
            template: Handlebars.templates['chat/controls/redirect'],

            /**
             * Map ui events to view methods
             * The view inherits events from
             * {@link Mibew.Views.Control.prototype.events}.
             * @type Object
             */
            events: _.extend(
                {},
                Mibew.Views.Control.prototype.events,
                {
                    'click': 'redirect'
                }
            ),

            /**
             * View initializer.
             */
            initialize: function() {
                Mibew.Objects.Models.user.on('change', this.render, this);
            },

            /**
             * Override Backbone.Marionette.ItemView.serializeData to pass some
             * extra fields to template.
             * @returns {Object} Template data
             */
            serializeData: function() {
                var data = this.model.toJSON();
                data.user = Mibew.Objects.Models.user.toJSON();
                return data;
            },

            /**
             * Display user redirection window
             */
            redirect: function() {
                var user = Mibew.Objects.Models.user;
                if (user.get('isAgent') && user.get('canPost')) {
                    var link = this.model.get('link');
                    if (link) {
                        // Redirect browser to user redirection page
                        var style = Mibew.Objects.Models.page.get('style');
                        var styleArg = '';
                        if (style) {
                            styleArg = ((link.indexOf('?') === -1) ? '?' : '&')
                                + 'style=' + style;
                        }
                        window.location.href = link.replace(/\&amp\;/g, '&')
                            + styleArg;
                    }
                }
            }
        }
    );

})(Mibew, Handlebars, _);