/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

(function(Mibew){

    /**
     * @class Represents message form model
     */
    Mibew.Models.MessageForm = Mibew.Models.Base.extend(
        /** @lends Mibew.Models.MessageForm.prototype */
        {
            /**
             * A list of default model values.
             * @type Object
             */
            defaults: {
                /**
                 * Array of predifined answers
                 * @type Array
                 */
                predefinedAnswers: [],

                /**
                 * Indicates if Enter or Ctrl+Enter should send message
                 * @type Boolean
                 */
                ignoreCtrl: false
            },

            /**
             * Post message.
             * Send message to the server and run callback function after that.
             * @param {String} msg Message to send
             */
            postMessage: function(msg) {
                // Get thread and user objects
                var thread = Mibew.Objects.thread;
                var user = Mibew.Objects.Models.user;

                // Check if user can post a message
                if (! user.get('canPost')) {
                    return;
                }

                // Triggers before post event
                this.trigger('before:post', this);

                // Store link to the object
                var self = this;

                // Post message to the server
                Mibew.Objects.server.callFunctions(
                    [{
                        "function": "post",
                        "arguments": {
                            "references": {},
                            "return": {},
                            "message": msg,
                            "threadId": thread.threadId,
                            "token": thread.token,
                            "user": (! user.get('isAgent'))
                        }
                    }],
                    function() {
                        self.trigger('after:post', self);
                    },
                    true
                );
            }
        }
    );


})(Mibew);