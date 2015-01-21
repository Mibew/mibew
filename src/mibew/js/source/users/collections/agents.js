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


