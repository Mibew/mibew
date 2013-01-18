/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

(function(Mibew, _) {

    /**
     * @class Represents an user.
     */
    Mibew.Models.ChatUser = Mibew.Models.User.extend(
        /** @lends Mibew.Models.ChatUser.prototype */
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
            )
        }
    );

})(Mibew, _);