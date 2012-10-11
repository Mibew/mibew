/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 *
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

/**
 * Create an instance of thread
 * @constructor
 */
var Thread = function(options) {

    /**
     * Id of the thread
     * @type Number
     */
    this.threadid = options.threadid || 0;

    /**
     * Thread's token. Uses for verify thread
     * @type Number
     */
    this.token = options.token || 0;

    /**
     * Last message id received by the thread
     * @type Number
     */
    this.lastid = options.lastid || 0;

    /**
     * Indicates if thread dispalys for user or for agent.
     *
     * Boolean true stands for user and boolean false stands for agent.
     * @type Boolean
     */
    this.user = options.user || false;
}