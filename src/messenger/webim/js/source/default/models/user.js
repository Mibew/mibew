/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

(function(Mibew, Backbone) {

    /**
     * @class Represents a user
     */
    Mibew.Models.User = Backbone.Model.extend(
        /** @lends Mibew.Models.User.prototype */
        {
            /**
             * A list of default model values.
             * @type Object
             */
            defaults: {

                /**
                 * Indicates if current application user is a user or an agent.
                 * @type Boolean
                 */
                isAgent: false,

                /**
                 * Indicates if user can post messages or not.
                 * @type Boolean
                 */
                canPost: true,

                /**
                 * Indicates if user typing a message at the moment.
                 * @type Boolean
                 */
                typing: false,

                /**
                 * Represents user name.
                 * Not applicable for agents.
                 * @type String
                 */
                name: '',

                /**
                 * Indicates if user can change name or not.
                 * Not applicable for agents.
                 * @type Boolean
                 */
                canChangeName: false,

                /**
                 * Indicates if user have default name.
                 * Not applicable for agents.
                 * @type Boolean
                 */
                dafaultName: true
            }
        }
    );

})(Mibew, Backbone);