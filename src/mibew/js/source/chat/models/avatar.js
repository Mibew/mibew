/*!
 * This file is a part of Mibew Messenger.
 *
 * Copyright 2005-2014 the original author or authors.
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
     * @class Represents agent's avatar
     */
    Mibew.Models.Avatar = Mibew.Models.Base.extend(
        /** @lends Mibew.Models.Avatar.prototype */
        {
            /**
             * A list of default model values.
             * @type Object
             */
            defaults : {
                /**
                 * An URL of the avatar image or false by default.
                 * @type String|Boolean
                 */
                imageLink: false
            },

            /**
             * Model initializer.
             */
            initialize: function() {

                /**
                 * Contain ids of registered by the model api functions
                 * @type Array
                 */
                this.registeredFunctions = [];

                // Register API function
                this.registeredFunctions.push(
                    Mibew.Objects.server.registerFunction(
                        'setupAvatar',
                        _.bind(this.apiSetupAvatar, this)
                    )
                );
            },

            // Model finalizer
            finalize: function() {
                // Unregister api functions
                for(var i = 0; i < this.registeredFunctions.length; i++) {
                    Mibew.Objects.server.unregisterFunction(
                        this.registeredFunctions[i]
                    );
                }
            },

            /**
             * Set avatar
             * This is an API function.
             * @param args {Object} An object of passed arguments
             */
            apiSetupAvatar: function(args) {
                if (args.imageLink) {
                    this.set({imageLink: args.imageLink});
                }
            }
        }
    );

})(Mibew, _);