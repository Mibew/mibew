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
     * @class Represents status message model
     */
    Mibew.Models.StatusMessage = Mibew.Models.Status.extend(
        /** @lends Mibew.Models.StatusMessage.prototype */
        {
            /**
             * A list of default model values.
             * The model inherits defaults from
             * {@link Mibew.Models.Status.prototype.defaults}.
             * @type Object
             */
            defaults: _.extend(
                {},
                Mibew.Models.Status.prototype.defaults,
                {
                    /**
                     * Text of the status message.
                     * @type String
                     */
                    message: '',

                    /**
                     * Indicates if status message is visible or not.
                     * @type Boolean
                     */
                    visible: false
                }
            ),

            /**
             * Returns model type
             * @returns {String} Model type
             */
            getModelType: function() {
                return 'StatusMessage'
            },

            /**
             * Update status message
             * @param {String} msg New status message
             */
            setMessage: function(msg) {
                // Update status message
                this.set({
                    message: msg,
                    visible: true
                });

                // Hide status message after a while
                this.autoHide();
            }
        }
    );

})(Mibew, _);