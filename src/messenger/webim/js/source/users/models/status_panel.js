/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
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


