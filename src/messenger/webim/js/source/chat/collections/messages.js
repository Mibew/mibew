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

                /**
                 * Contains ids of periodically called functions
                 * @type Array
                 */
                this.periodicallyCalled = [];

                // Periodically try to get new messages
                this.periodicallyCalled.push(
                    Mibew.Objects.server.callFunctionsPeriodically(
                        _.bind(this.updateMessagesFunctionBuilder, this),
                        _.bind(this.updateMessages, this)
                    )
                );
            },

            /**
             * Collection finalizer
             */
            finalize: function() {
                // Stop call functions periodically
                for (var i = 0; i < this.periodicallyCalled.length; i++) {
                    Mibew.Objects.server.stopCallFunctionsPeriodically(
                        this.periodicallyCalled[i]
                    );
                }
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

                // Use shortcut for KIND_PLUGIN
                var kindPlugin = Mibew.Models.Message.prototype.KIND_PLUGIN;

                // Get all new messages
                var newMessages = [];
                var messageData, pluginName, eventName, eventArgs;
                for(var i = 0, length = args.messages.length; i < length; i++) {
                    messageData = args.messages[i];
                    if (messageData.kind != kindPlugin) {
                        // Message have one of the core kinds. Just store it.
                        newMessages.push(
                            new Mibew.Models.Message(messageData)
                        );
                        continue;
                    }

                    // Message have KIND_PLUGIN kind and need to be processed
                    // by plugins to know how to display it.
                    //
                    // Message treat as data object with following fields:
                    //  - 'plugin': string, name of the plugin which sent the
                    //    message;
                    //  - 'data': object, some data sent by the plugin.

                    // Check if message is an real Object
                    if ((typeof messageData.message != 'object')
                        || (messageData.message === null)) {
                        continue;
                    }

                    // Prepare event name.
                    //
                    // If plugin name was specified it will be
                    // 'process:<plugin_name>:plugin:message' and
                    // 'process:plugin:message' otherwise.
                    pluginName = messageData.message.plugin || false;
                    eventName = 'process:'
                        + ((pluginName !== false) ? pluginName + ':' : '')
                        + 'plugin:message';

                    // Prepare event arguments.
                    //
                    // It is an object with following fields:
                    //  - 'messageData': object which contains message data
                    //    passed from server.
                    //  - 'model': message model initialized by the plugin or
                    //    boolean false if message should not be displayed. By
                    //    default it field equals to boolean false.
                    eventArgs = {
                        'messageData': messageData,
                        'model': false
                    }

                    // Trigger event. See description of eventName and eventArgs
                    // above.
                    this.trigger(eventName, eventArgs);

                    if (eventArgs.model) {
                        // Store custom plugin message in the collection
                        newMessages.push(
                            eventArgs.model
                        );
                    }
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