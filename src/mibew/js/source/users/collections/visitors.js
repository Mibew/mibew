/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */

(function(Mibew, Backbone, _){

    /**
     * @class Represents visitors collection
     */
    Mibew.Collections.Visitors = Backbone.Collection.extend(
        /** @lends Mibew.Collections.Visitors.prototype */
        {
            /**
             * Model type of the collection items
             * @type Function
             */
            model: Mibew.Models.Visitor,

            /**
             * Collection initializer
             */
            initialize: function() {
                // Register some shortcuts
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
                                'function': 'updateVisitors',
                                'arguments': {
                                    'agentId': agent.id,
                                    'return': {
                                        'visitors': 'visitors'
                                    },
                                    'references': {}
                                }
                            }
                        ];
                    },
                    _.bind(this.updateVisitors, this)
                );
            },

            /**
             * Use for sort visitors in collection.
             * By default visitors sort by firstTime field.
             * Triggers 'sort:field' event after sort field generated.
             * @param {Mibew.Models.Visitor} visitor Visitor model
             */
            comparator: function(visitor) {
                // Create default sort field
                var sort = {
                    field: visitor.get('firstTime').toString()
                }

                // Trigger event to provide an ability to change sorting order
                this.trigger('sort:field', visitor, sort);

                // Return sort field
                return sort.field;
            },

            /**
             * Update visitors list.
             * Trigger 'before:update:visitors' event and pass array of raw
             * visitors data as argument to event handler.
             * Also trigger 'after:update:visitors' event.
             * @param {Object} args Arguments returned from server
             */
            updateVisitors: function(args) {
                if (args.errorCode == 0) {
                    // Fix time difference between server and client
                    var delta;
                    if (args.currentTime) {
                        delta = Math.round((new Date()).getTime() / 1000)
                            - args.currentTime;
                    } else {
                        delta = 0;
                    }
                    for(var i = 0, l = args.visitors.length; i < l; i++) {
                        args.visitors[i].lastTime = parseInt(args.visitors[i].lastTime) + delta;
                        args.visitors[i].firstTime = parseInt(args.visitors[i].firstTime) + delta;
                    }

                    // Trigger event. Event handlers can change visitors info
                    this.trigger('before:update:visitors', args.visitors);

                    // Update collection
                    this.reset(args.visitors);

                    // Trigger event
                    this.trigger('after:update:visitors');
                }
            }

        }
    );

})(Mibew, Backbone, _);


