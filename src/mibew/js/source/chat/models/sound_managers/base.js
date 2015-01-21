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

(function(Mibew, Backbone) {

    /**
     * @class Represents Base helper model which manage all sounds
     */
    Mibew.Models.BaseSoundManager = Backbone.Model.extend(
        /** @lends Mibew.Models.BaseSoundManager.prototype */
        {
            /**
             * A list of default model values.
             * @type Object
             */
            defaults: {
                /**
                 * Indicates if sound enabled or not
                 */
                enabled: true
            },

            /**
             * Play sound file if sound enabled.
             * @param {String} file Sound file to play
             */
            play: function(file) {
                if (this.get('enabled')) {
                    Mibew.Utils.playSound(file);
                }
            }
        }
    );

})(Mibew, Backbone);