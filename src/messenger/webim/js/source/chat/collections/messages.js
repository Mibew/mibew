/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
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
             * Collection initializer.
             */
            initialize: function() {
                // Periodically try to get new messages
                Mibew.Objects.server.callFunctionsPeriodically(
                    _.bind(this.updateMessagesFunctionBuilder, this),
                    _.bind(this.updateMessages, this)
                );
            },

            /**
             * Update messages if they are exist.
             * @param args {Object} An object of passed arguments
             */
            updateMessages: function(args) {

                // Update last message id
                if (args.lastId) {
                    Mibew.Objects.Models.thread.set({lastId: args.lastId});
                }

                // Get all new messages
                var newMessages = [];
                for(var i = 0, length = args.messages.length; i < length; i++) {
                    // Store message
                    newMessages.push(
                        new Mibew.Models.Message(args.messages[i])
                    );
                }

                // Add new messages to the message collection if there are any
                // messages
                if (newMessages.length > 0) {
                    this.add(newMessages);
                }
            },

            /**
             * Builds updateMessages function, that should be called
             * periodically at the server side
             * @returns {Object[]} Array of functions objects
             */
            updateMessagesFunctionBuilder: function() {
                // Get thread and user objects
                var thread = Mibew.Objects.Models.thread;
                var user = Mibew.Objects.Models.user;

                // Build functions list
                return [
                    {
                        "function": "updateMessages",
                        "arguments": {
                            "return": {
                                'messages': 'messages',
                                'lastId': 'lastId'
                            },
                            "references": {},
                            "threadId": thread.get('id'),
                            "token": thread.get('token'),
                            "lastId": thread.get('lastId'),
                            "user": (! user.get('isAgent'))
                        }
                    }
                ];
            },

            /**
             * Override Backbone.Collection.add method to call additional event
             */
            add: function() {
                // Get arguments list
                var args = Array.prototype.slice.apply(arguments);
                // Call method of the parent class
                var res = Backbone.Collection.prototype.add.apply(this, args);
                // Triggers additional event
                this.trigger('multiple:add');
                return res;
            }
        }
    );

})(Mibew, Backbone, _);