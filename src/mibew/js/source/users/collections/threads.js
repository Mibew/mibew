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
     * @class Represents threads collection
     */
    Mibew.Collections.Threads = Backbone.Collection.extend(
        /** @lends Mibew.Collections.Threads.prototype */
        {
            /**
             * Model type of the collection items
             * @type Function
             */
            model: Mibew.Models.QueuedThread,

            /**
             * Collection initializer
             */
            initialize: function() {
                // Initialize fields and methods

                /**
                 * Last threads revision number. Prevent transfering not
                 * modified threads.
                 * @type Number
                 * @fieldOf Mibew.Collections.Threads
                 */
                this.revision = 0;

                // Register some shortcuts
                var self = this;
                var agent = Mibew.Objects.Models.agent;

                // Call updateThreads periodically at the server
                Mibew.Objects.server.callFunctionsPeriodically(
                    function(){
                        return [
                            {
                                'function': 'currentTime',
                                'arguments': {
                                    'agentId': agent.id,
                                    'return': {
                                        'time': 'currentTime'
                                    },
                                    'references': {}
                                }
                            },
                            {
                                'function': 'updateThreads',
                                'arguments': {
                                    'agentId': agent.id,
                                    'revision': self.revision,
                                    'return': {
                                        'threads': 'threads',
                                        'lastRevision': 'lastRevision'
                                    },
                                    'references': {}
                                }
                            }
                        ];
                    },
                    _.bind(this.updateThreads, this)
                );
            },

            /**
             * Use for sort threads in collection.
             * By default threads sort by state and waiting time.
             * Triggers 'sort:field' event after sort field generated.
             * @param {Mibew.Models.QueuedThread} thread Thread model
             */
            comparator: function(thread) {
                // Create default sort field
                var sort = {
                    field: thread.get('waitingTime').toString()
                }

                // Trigger event to provide an ability to change sorting order
                this.trigger('sort:field', thread, sort);

                // Return sort field
                return sort.field;
            },

            /**
             * Update threads list.
             * Trigger 'before:update:threads' event and pass array of raw
             * threads data as argument to event handler.
             * Also trigger 'after:update:threads' event.
             * @param {Object} args Arguments returned from server
             */
            updateThreads: function(args) {
                if (args.errorCode == 0) {

                    if (args.threads.length > 0) {
                        // Fix time difference between server and client
                        var delta;
                        if (args.currentTime) {
                            delta = Math.round((new Date()).getTime() / 1000)
                                - args.currentTime;
                        } else {
                            delta = 0;
                        }
                        for(var i = 0, l = args.threads.length; i < l; i++) {
                            args.threads[i].totalTime
                                = parseInt(args.threads[i].totalTime) + delta;
                            args.threads[i].waitingTime
                                = parseInt(args.threads[i].waitingTime) + delta;
                        }

                        // Trigger event. Event handlers can change threads info
                        this.trigger('before:update:threads', args.threads);

                        // Create shortcuts for thread states
                        var stateClosed = Mibew.Models.Thread.prototype.STATE_CLOSED;
                        var stateLeft = Mibew.Models.Thread.prototype.STATE_LEFT;

                        // Define empty array for threads that should be remove
                        var remove = [];

                        // Update threads list
                        this.set(args.threads, {remove: false, sort: false});

                        // Get closed and left thread. Collect them into
                        // remove array
                        remove = this.filter(function(thread) {
                            return (thread.get('state') == stateClosed
                                || thread.get('state') == stateLeft);
                        });
                        // Remove closed and left threads
                        if (remove.length > 0) {
                            this.remove(remove);
                        }

                        // Sort residual collection
                        this.sort();

                        // Trigger event
                        this.trigger('after:update:threads');
                    }
                    this.revision = args.lastRevision;
                }
            }
        }
    );

})(Mibew, Backbone, _);


