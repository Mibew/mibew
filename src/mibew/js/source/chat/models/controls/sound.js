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
     * @class Represents toggle sound control model
     */
    Mibew.Models.SoundControl = Mibew.Models.Control.extend(
        /** @lends Mibew.Models.SoundControl.prototype */
        {
            /**
             * A list of default model values.
             * The model inherits defaults from
             * {@link Mibew.Models.Control.prototype.defaults}.
             * @type Object
             */
            defaults: _.extend(
                {},
                Mibew.Models.Control.prototype.defaults,
                {
                    /**
                     * Indicates if sound enable.
                     * @type Boolean
                     */
                    enabled: true
                }
            ),

            /**
             * Toggles sound control state
             */
            toggle: function() {
                var enabled = ! this.get('enabled');

                // Toggle sound manager state
                Mibew.Objects.Models.soundManager.set({'enabled': enabled});

                // Update self state
                this.set({'enabled': enabled});
            },

            /**
             * Returns model type
             * @returns {String} Model type
             */
            getModelType: function() {
                return 'SoundControl'
            }
        }
    );

})(Mibew, _);