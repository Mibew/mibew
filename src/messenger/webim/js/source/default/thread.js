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
    Mibew.Thread = function(options) {

        /**
         * Id of the thread
         * @type Number
         */
        this.threadId = options.threadId || 0;

        /**
         * Thread's token. Uses for verify thread
         * @type Number
         */
        this.token = options.token || 0;

        /**
         * Last message id received by the thread
         * @type Number
         */
        this.lastId = options.lastId || 0;
    }
})(Mibew);