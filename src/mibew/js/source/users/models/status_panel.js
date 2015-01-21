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

(function(Mibew){

    /**
     * @class Represents a status panel
     */
    Mibew.Models.StatusPanel = Mibew.Models.Base.extend(
        /** @lends Mibew.Models.StatusPanel.prototype */
        {
            /**
             * A list of default model values.
             * @type Object
             */
            defaults: {
                /**
                 * Status message
                 * @type String
                 */
                message: ''
            },

            /**
             * Set status message
             * @param {String} message New status message
             */
            setStatus: function(message) {
                this.set({'message': message});
            },

            /**
             * Changes agent status
             */
            changeAgentStatus: function() {
                var agent = Mibew.Objects.Models.agent;
                if (agent.get('away')) {
                    agent.available();
                } else {
                    agent.away();
                }
            }
        }
    );

})(Mibew);


