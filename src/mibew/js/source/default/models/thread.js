/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
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
             * Inherits values from Mibew.Models.User
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