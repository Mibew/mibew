/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 *
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
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
            STATE_LEFT: 5

            /** End of thread state constants */
        }
    );
})(Mibew);