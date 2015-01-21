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

(function(Mibew, Backbone, _){

    /**
     * @class Represents messages list
     */
    Mibew.Collections.Messages = Backbone.Collection.extend(
        /** @lends Mibew.Collections.Message.prototype */
        {
            /**
             * Default contructor for model
             * @type Function
             */
            model: Mibew.Models.Message,

            /**
             * Update messages in collection.
             *
             * Skip messages with empty text body.
             * @param rawMessages {Array} Array of row message models data.
             */
            updateMessages: function(rawMessages) {
                // Reject all messages with empty text body
                var newMessages = [];
                for(var i = 0; i < rawMessages.length; i++) {
                    if (! rawMessages[i].message) {
                        continue;
                    }
                    newMessages.push(rawMessages[i]);
                }

                // Add new messages to the collection
                if (newMessages.length > 0) {
                    this.add(newMessages);
                }
            }
        }
    );

})(Mibew, Backbone, _);