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
     * @class Close control model
     */
    Mibew.Models.CloseControl = Mibew.Models.Control.extend(
        /** @lends Mibew.Models.CloseControl.prototype */
        {
            /**
             * Returns model type
             * @returns {String} Model type
             */
            getModelType: function() {
                return 'CloseControl';
            },

            /**
             * Close chat thread at the server
             *
             * If something went wrong update status message otherwise close
             * chat window
             *
             * @todo May be move to Mibew.Thread class
             */
            closeThread: function() {
                // Get thread and user objects
                var thread = Mibew.Objects.Models.thread;
                var user = Mibew.Objects.Models.user;
                // Send request to the server
                Mibew.Objects.server.callFunctions(
                    [{
                        "function": "close",
                        "arguments": {
                            "references": {},
                            "return": {"closed": "closed"},
                            "threadId": thread.get('id'),
                            "token": thread.get('token'),
                            "lastId": thread.get('lastId'),
                            "user": (! user.get('isAgent'))
                        }
                    }],
                    function(args){
                        if (args.closed) {
                            Mibew.Utils.closeChatPopup();
                        } else {
                            // Something went wrong. Display error message
                            Mibew.Objects.Models.Status.message.setMessage(
                                args.errorMessage || 'Cannot close'
                            );
                        }
                    },
                    true
                );
            }
        }
    );

})(Mibew);