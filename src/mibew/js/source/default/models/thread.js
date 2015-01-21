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

(function(Mibew){
    /**
     * Create an instance of thread
     * @class
     */
    Mibew.Models.Thread = Mibew.Models.Base.extend(
        /** @lends Mibew.Models.Thread.prototype */
        {
            /**
             * A list of default model values.
             * @type Object
             */
            defaults: {
                /**
                 * Id of the thread
                 * @type Number
                 */
                id: 0,

                /**
                 * Thread's token. Uses for verify thread
                 * @type Number
                 */
                token: 0,

                /**
                 * Last message id received by the thread
                 * @type Number
                 */
                lastId: 0,

                /**
                 * ID of the user related with the chat.
                 * @type String
                 */
                userId: null,

                /**
                 * ID of the agent related with the chat.
                 */
                agentId: null,

                /**
                 * Thread's state
                 * @type Number
                 */
                state: null
            },

            /** Thread state constants */

            /**
             * User in the users queue
             */
            STATE_QUEUE: 0,
            /**
             * User waiting for operator
             */
            STATE_WAITING: 1,
            /**
             * Conversation in progress
             */
            STATE_CHATTING: 2,
            /**
             * Thread closed
             */
            STATE_CLOSED: 3,
            /**
             * Thread just created
             */
            STATE_LOADING: 4,
            /**
             * User left message without starting a conversation
             */
            STATE_LEFT: 5,
            /**
             * Visitor was invited to chat by operator
             */
            STATE_INVITED: 6

            /** End of thread state constants */
        }
    );
})(Mibew);