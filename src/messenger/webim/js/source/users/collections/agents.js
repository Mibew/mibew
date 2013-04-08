/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
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


