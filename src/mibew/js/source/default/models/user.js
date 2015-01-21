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

(function(Mibew) {

    /**
     * @class Represents an user
     */
    Mibew.Models.User = Mibew.Models.Base.extend(
        /** @lends Mibew.Models.User.prototype */
        {
            /**
             * A list of default model values.
             * @type Object
             */
            defaults: {

                /**
                 * Indicates if current application user is a user or an agent.
                 * @type Boolean
                 */
                isAgent: false,

                /**
                 * Represents user name.
                 * @type String
                 */
                name: ''
            }
        }
    );

})(Mibew);