/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

(function(Mibew, Backbone) {

    /**
     * @class Represents an user
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
                 * Represents user name.
                 * @type String
                 */
                name: ''
            }
        }
    );

})(Mibew, Backbone);