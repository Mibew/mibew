/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
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