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

(function(Mibew, _){

    /**
     * @class Represents typing status model
     */
    Mibew.Models.StatusTyping = Mibew.Models.Status.extend(
        /** @lends Mibew.Models.StatusTyping.prototype */
        {
            /**
             * A list of default model values.
             *
             * The model inherits defaults from
             * {@link Mibew.Models.Status.prototype.defaults}.
             * @type Object
             */
            defaults: _.extend(
                {},
                Mibew.Models.Status.prototype.defaults,
                {
                    /**
                     * Indicates if status element is visible or not.
                     * @type Boolean
                     */
                    visible: false,

                    /**
                     * Time in milliseconds after that status message will be
                     * cleaned.
                     * @type Number
                     */
                    hideTimeout: 2000
                }
            ),

            /**
             * Returns model type
             * @returns {String} Model type
             */
            getModelType: function() {
                return 'StatusTyping'
            },

            /**
             * Make the status element visible
             * @todo May be move it to the view!?
             */
            show: function() {
                // Make typing status visible
                this.set({visible: true});

                // Hide it after a while
                this.autoHide();
            }
        }
    );

})(Mibew, _);