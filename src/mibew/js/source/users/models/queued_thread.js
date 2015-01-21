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

(function(Mibew, _){

    /**
     * Holds thread controls constructors
     * @type Array
     */
    var controlsConstructors = [];

    /**
     * Prepresent thread in users queue
     * @class
     */
    var QueuedThread = Mibew.Models.QueuedThread = Mibew.Models.Thread.extend(
        /** @lends Mibew.Models.QueuedThread.prototype */
        {
            /**
             * A list of default model values.
             * Inherits values from Mibew.Models.Thread
             * @type Object
             */
            defaults: _.extend(
                {},
                Mibew.Models.Thread.prototype.defaults,
                {
                    /**
                     * Collection of thread controls
                     * @type Mibew.Collections.Controls
                     */
                    controls: null,

                    /**
                     * Name of the user
                     * @type String
                     */
                    userName: '',

                    /**
                     * Ip address of the user
                     * @type String
                     */
                    userIp: '',

                    /**
                     * Full remote address returned by web server. Generally
                     * equals to userIp.
                     * @type String
                     */
                    remote: '',

                    /**
                     * User agent
                     * @type String
                     */
                    userAgent: '',

                    /**
                     * Agent name
                     * @type String
                     */
                    agentName: '',

                    /**
                     * Indicates if agent can open thread
                     * @type Boolean
                     */
                    canOpen: false,

                    /**
                     * Indicates if agent can view thread
                     * @type Boolean
                     */
                    canView: false,

                    /**
                     * Indicates if agent can ban the user
                     * @type Boolean
                     */
                    canBan: false,

                    /**
                     * Contains ban info if user already blocked or boolean
                     * false otherwise.
                     * @type Boolean|Object
                     */
                    ban: false,

                    /**
                     * Unix timestamp when thread was started
                     * @type Number
                     */
                    totalTime: 0,

                    /**
                     * Unix timestamp when user begin wait for agent
                     * @type Number
                     */
                    waitingTime: 0,

                    /**
                     * First message from user to operator
                     * @type String
                     */
                    firstMessage: null
                }
            ),

            /**
             * Model initializer.
             * Create controls collection and store it in the model field.
             */
            initialize: function() {
                var self = this;
                var controls = [];
                var constructors = QueuedThread.getControls();
                for (var i = 0, l = constructors.length; i < l; i++) {
                    controls.push(new constructors[i]({thread: self}));
                }
                this.set({
                    controls: new Mibew.Collections.Controls(controls)
                });
            }
        },


        /** @lends Mibew.Models.QueuedThread */
        {
            /**
             * Add thread control constructor
             * @static
             * @param {Function} Mibew.Models.Control or inherited constructor
             */
            addControl: function(control) {
                controlsConstructors.push(control)
            },

            /**
             * Returns list of thread controls constructors
             * @static
             * @returns {Array} List of controls constructors
             */
            getControls: function() {
                return controlsConstructors;
            }
        }
    );
})(Mibew, _);