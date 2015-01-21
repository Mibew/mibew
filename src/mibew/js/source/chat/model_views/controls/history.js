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
     * @class Represents History control view
     */
    Mibew.Views.HistoryControl = Mibew.Views.Control.extend(
        /** @lends Mibew.Views.HistoryControl.prototype */
        {
            /**
             * Template function
             * @type Function
             */
            template: Handlebars.templates['chat/controls/history'],

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
                    'click': 'showHistory'
                }
            ),

            /**
             * Dispalay history window
             */
            showHistory: function() {
                var user = Mibew.Objects.Models.user;
                var link = this.model.get('link');
                if (user.get('isAgent') && link) {
                    var winParams = Mibew.Utils.buildWindowParams(this.model.get('windowParams'));

                    // TODO: Kill &amp; at the server side
                    link = link.replace('&amp;', '&', 'g');

                    var newWindow = window.open(link, 'UserHistory', winParams);
                    if (newWindow !== null) {
                        newWindow.focus();
                        newWindow.opener=window;
                    }
                }
            }
        }
    );

})(Mibew, Handlebars, _);