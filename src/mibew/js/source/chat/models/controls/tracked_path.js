/*!
 * This file is a part of Mibew Messenger.
 *
 * Copyright 2005-2023 the original author or authors.
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
     * @class Tracked path control model
     */
    Mibew.Models.TrackedPathControl = Mibew.Models.Control.extend(
        /** @lends Mibew.Models.TrackedPathControl.prototype */
        {
            /**
             * A list of default model values.
             *
             * The model inherits defaults from
             * {@link Mibew.Models.Control.prototype.defaults}.
             * @type Object
             */
            defaults: _.extend(
                {},
                Mibew.Models.Control.prototype.defaults,
                {
                    /**
                     * An URL of the tracked path page or false by default.
                     * @type String|Boolean
                     */
                    link: false,

                    /**
                     * Params string for tracked path popup window
                     * @type String
                     */
                    windowParams: ''
                }
            ),

            /**
             * Returns model type
             * @returns {String} Model type
             */
            getModelType: function() {
                return 'TrackedPathControl';
            }

        }
    );

})(Mibew, _);