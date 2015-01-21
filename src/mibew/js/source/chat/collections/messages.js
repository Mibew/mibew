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

                    // Check if message is an real Object
                    if ((typeof messageData.data != 'object')
                        || (messageData.data === null)) {
                        continue;
                    }

                    // Prepare event name.
                    //
                    // If plugin name was specified it will be
                    // 'process:<plugin_name>:plugin:message' and
                    // 'process:plugin:message' otherwise.
                    pluginName = messageData.plugin || false;
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
                        // Check if plugin set message id
                        if (! eventArgs.model.get('id')) {
                            // Message must have an id, set it
                            eventArgs.model.set({'id': messageData.id});
                        }

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