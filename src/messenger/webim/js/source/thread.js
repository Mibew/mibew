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

/** Message kinds section */

/**
 * Message sent by user
 */
Thread.prototype.KIND_USER = 1;
/**
 * Message sent by operator
 */
Thread.prototype.KIND_AGENT = 2;
/**
 * Hidden system message to operator
 */
Thread.prototype.KIND_FOR_AGENT = 3;
/**
 * System messages for user and operator
 */
Thread.prototype.KIND_INFO = 4;
/**
 * Message for user if operator have connection problems
 */
Thread.prototype.KIND_CONN = 5;
/**
 * System message about some events (like rename).
 */
Thread.prototype.KIND_EVENTS = 6;
/**
 * Message with operators avatar
 *
 * This kind of message leaved only for compatibility with core
 */
Thread.prototype.KIND_AVATAR = 7;

/** End of Message kinds section */