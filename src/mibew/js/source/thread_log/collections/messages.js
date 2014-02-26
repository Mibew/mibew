/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
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