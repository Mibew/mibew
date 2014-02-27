/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */

(function(Mibew, Backbone, _){

    /**
     * @class Represents collection of agents
     */
    Mibew.Collections.Agents = Backbone.Collection.extend(
        /** @lends Mibew.Collections.Agents.prototype */
        {
            /**
             * Model type of the collection items
             */
            model: Mibew.Models.Agent,

            /**
             * Use for sort controls in collection
             * @param {Backbone.Model} model Agent model
             */
            comparator: function(model) {
                return model.get('name');
            },

            /**
             * Collection initializer
             */
            initialize: function() {
                // Register some shortcuts
                var agent = Mibew.Objects.Models.agent;

                // Call updateOperators periodically at the server
                Mibew.Objects.server.callFunctionsPeriodically(
                    function(){
                        return [
                            {
                                'function': 'updateOperators',
                                'arguments': {
                                    'agentId': agent.id,
                                    'return': {
                                        'operators': 'operators'
                                    },
                                    'references': {}
                                }
                            }
                        ];
                    },
                    _.bind(this.updateOperators, this)
                );
            },

            /**
             * Update available agents.
             * @param {Object} args Arguments from the server
             */
            updateOperators: function(args) {
                this.set(args.operators);
            }
        }
    );

})(Mibew, Backbone, _);


