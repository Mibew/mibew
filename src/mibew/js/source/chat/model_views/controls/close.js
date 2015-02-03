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
     * @class Represents Close control view
     */
    Mibew.Views.CloseControl = Mibew.Views.Control.extend(
        /** @lends Mibew.Views.CloseControl.prototype */
        {
            /**
             * Template function
             * @type Function
             */
            template: Handlebars.templates['chat/controls/close'],

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
                    'click': 'closeThread'
                }
            ),

            /**
             * Display confirmation dialog and close chat window
             */
            closeThread: function() {
                // Show confirmation message if can
                var confirmMessage = Mibew.Localization.trans('Are you sure want to leave chat?'),
                    context = this;
                if (confirmMessage !== false) {
                    Mibew.Utils.confirm(confirmMessage, function(value) {
                        if (value) {
                            context.model.closeThread();
                        }
                    });
                }
            }
        }
    );

})(Mibew, Handlebars, _);