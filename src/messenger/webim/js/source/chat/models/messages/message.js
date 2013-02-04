/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

(function(Mibew){

    /**
     * @class Base class for message models
     */
    Mibew.Models.Message = Mibew.Models.Base.extend(
        /** @lends Mibew.Models.Message.prototype */
        {
            /**
             * Default values of model
             */
            defaults : {
                /**
                 * Text of the message
                 * @type String
                 */
                message: ''
            },

            /** Message kind constants */

            /** Message sent by user. */
            KIND_USER: 1,

            /** Message sent by operator */
            KIND_AGENT: 2,

            /** Hidden system message to operator */
            KIND_FOR_AGENT: 3,

            /** System messages for user and operator */
            KIND_INFO: 4,

            /** Message for user if operator have connection problems */
            KIND_CONN: 5,

            /** System message about some events (like rename). */
            KIND_EVENTS: 6,

            /**
             * Message with operators avatar
             *
             * This kind of message leaved only for compatibility with core
             */
            KIND_AVATAR: 7

            /** End of message kind constants */
        }
    );

})(Mibew);