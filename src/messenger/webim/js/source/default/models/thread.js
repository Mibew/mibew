/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 *
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

(function(Mibew){
    /**
     * Create an instance of thread
     * @constructor
     */
    Mibew.Models.Thread = Mibew.Models.Base.extend(
        /** @lends Mibew.Models.Thread.prototype */
        {
            /**
             * A list of default model values.
             * Inherits values from Mibew.Models.User
             * @type Object
             */
            defaults: {
                /**
                 * Id of the thread
                 * @type Number
                 */
                id: 0,

                /**
                 * Thread's token. Uses for verify thread
                 * @type Number
                 */
                token: 0,

                /**
                 * Last message id received by the thread
                 * @type Number
                 */
                lastId: 0
            }
        }
    );
})(Mibew);