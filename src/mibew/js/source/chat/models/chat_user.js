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

(function(Mibew, _) {

    /**
     * @class Represents an user.
     */
    Mibew.Models.ChatUser = Mibew.Models.User.extend(
        /** @lends Mibew.Models.ChatUser.prototype */
        {
            /**
             * A list of default model values.
             * Inherits values from Mibew.Models.User
             * @type Object
             */
            defaults: _.extend(
                {},
                Mibew.Models.User.prototype.defaults,
                {
                    /**
                    * Indicates if user can post messages or not.
                    * @type Boolean
                    */
                    canPost: true,

                    /**
                    * Indicates if user typing a message at the moment.
                    * @type Boolean
                    */
                    typing: false,

                    /**
                    * Indicates if user can change name or not.
                    * Not applicable for agents.
                    * @type Boolean
                    */
                    canChangeName: false,

                    /**
                    * Indicates if user have default name.
                    * Not applicable for agents.
                    * @type Boolean
                    */
                    dafaultName: true
                }
            )
        }
    );

})(Mibew, _);