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
     * Holds visitor controls constructors
     * @type Array
     */
    var controlsConstructors = [];

    /**
     * @class Represents a visitor.
     */
    var Visitor = Mibew.Models.Visitor = Mibew.Models.User.extend(
        /** @lends Mibew.Models.Visitor.prototype */
        {
            /**
             * A list of default model values.
             * Inherits values from Mibew.Models.User
             * @type Object
             */
            defaults: _.extend(
                {},
                Mibew.Models.User.prototype.defaults,
                {
                    /**
                     * Collection of visitor controls
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
                     * Unix timestamp when visitor was first time observed
                     * on site
                     * @type Number
                     */
                    firstTime: 0,

                    /**
                     * Unix timestamp when visitor was first time observed
                     * on site
                     * @type Number
                     */
                    lastTime: 0,

                    /**
                     * Total invitations count
                     * @type Number
                     */
                    invitations: 0,

                    /**
                     * Total chats count with visitor
                     * @type Number
                     */
                    chats: 0,

                    /**
                     * Information about invitation or booean false if there is
                     * no invitation yet.
                     *
                     * Information object contains following keys:
                     *  - 'agentName': name of the agent who invited the visitor
                     *  - 'time': invitation time
                     * @type Object|Boolean
                     */
                    invitationInfo: false
                }
            ),

            /**
             * Model initializer.
             * Create controls collection and store it in the model field.
             */
            initialize: function() {
                var self = this;
                var controls = [];
                var constructors = Visitor.getControls();
                for (var i = 0, l = constructors.length; i < l; i++) {
                    controls.push(new constructors[i]({visitor: self}));
                }
                this.set({
                    controls: new Mibew.Collections.Controls(controls)
                });
            }
        },

        /** @lends Mibew.Models.Visitor */
        {
            /**
             * Add visitor control constructor
             * @static
             * @param {Function} Mibew.Models.Control or inherited constructor
             */
            addControl: function(control) {
                controlsConstructors.push(control)
            },

            /**
             * Returns list of visitor controls constructors
             * @static
             * @returns {Array} List of controls constructors
             */
            getControls: function() {
                return controlsConstructors;
            }
        }
    );

})(Mibew, _);
