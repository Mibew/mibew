/*!
 * This file is a part of Mibew Messenger.
 *
 * Copyright 2005-2022 the original author or authors.
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
     * @class Represents Send mail control View
     */
    Mibew.Views.SendMailControl = Mibew.Views.Control.extend(
        /** @lends Mibew.Views.SendMailControl.prototype */
        {
            /**
             * Template function
             * @type Function
             */
            template: Handlebars.templates['chat/controls/send_mail'],

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
                    'click': 'sendMail'
                }
            ),

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
             * Load and display send mail window
             */
            sendMail: function() {
                var link = this.model.get('link');
                var page = Mibew.Objects.Models.page;
                if (link) {
                    var winParams = Mibew.Utils.buildWindowParams(this.model.get('windowParams'));

                    var style = page.get('style');
                    var styleArg = '';
                    if (style) {
                        styleArg = ((link.indexOf('?') === -1) ? '?' : '&')
                            + 'style=' + style;
                    }

                    // TODO: Kill &amp; at the server side
                    link = link.replace(/\&amp\;/g, '&') + styleArg;

                    var newWindow = window.open(link, 'ForwardMail', winParams);
                    if (newWindow !== null) {
                        newWindow.focus();
                        newWindow.opener=window;
                    }
                }
            }
        }
    );

})(Mibew, Handlebars, _);