/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */

(function(Mibew) {

    /**
     * @class Represents an user
     */
    Mibew.Models.User = Mibew.Models.Base.extend(
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

})(Mibew);