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
                var thread = Mibew.Objects.Models.thread;
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
                            "threadId": thread.get('id'),
                            "token": thread.get('token'),
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