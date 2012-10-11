/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

var MessageView = function() {

    /**
     * List of replacements pairs
     * @type Object
     * @private
     */
    var badCharList = {
        "<": "&lt;",
        ">": "&gt;",
        "&": "&amp;",
        '"': "&quot;",
        "'": "&#x27;",
        "`": "&#x60;"
    }

    /**
     * Regular expression for characters that must be replaced by HTML entities
     * @type RegExp
     * @private
     */
    var badCharRegEx = /[&<>'"`]/g;

    /**
     * Retrun message kind shortening name corresponding to message kind code
     *
     * @param {Number} kind Message kind code
     * @returns {String} Message kind shortening name
     */
    this.kindToString = function(kind) {
        if (kind == this.KIND_USER) {
            return "user";
        }
        if (kind == this.KIND_AGENT) {
            return "agent";
        }
        if (kind == this.KIND_FOR_AGENT) {
            return "hidden";
        }
        if (kind == this.KIND_INFO) {
            return "inf";
        }
        if (kind == this.KIND_CONN) {
            return "conn";
        }
        if (kind == this.KIND_EVENTS) {
            return "event";
        }
        return "";
    }

    /**
     * Replace HTML special characters('<', '>', '&', "'", '"', '`') by
     * corresponding HTML entities.
     *
     * @param {String} str Unescaped string
     * @returns {String} Escaped string
     */
    this.escapeString = function(str) {
        return str.replace(
            badCharRegEx,
            function(chr) {
                return badCharList[chr] || "&amp;";
            }
        );
    }

    /**
     * Prepare message and substitute it into message's template
     *
     * @param {Object} msg Message object
     * @returns {String} Rendered message
     */
    this.themeMessage = function(msg) {
        // Check template existance
        if (! Handlebars.templates.message) {
            throw new Error('There is no template for message loaded!');
        }
        // Check message kind
        if (msg.kind == this.KIND_AVATAR) {
            throw new Error('KIND_AVATAR message kind is deprecated at window!');
        }
        // Add message fields
        msg.allowFormating = (msg.kind != this.KIND_USER && msg.kind != this.KIND_AGENT);
        msg.kindName = this.kindToString(msg.kind);
        msg.message = this.escapeString(msg.message);
        // Theme message
        return Handlebars.templates.message(msg);
    }
}

/** Message kind constants */

/**
 * Message sent by user.
 */
MessageView.prototype.KIND_USER = 1;
/**
 * Message sent by operator
 */
MessageView.prototype.KIND_AGENT = 2;
/**
 * Hidden system message to operator
 */
MessageView.prototype.KIND_FOR_AGENT = 3;
/**
 * System messages for user and operator
 */
MessageView.prototype.KIND_INFO = 4;
/**
 * Message for user if operator have connection problems
 */
MessageView.prototype.KIND_CONN = 5;
/**
 * System message about some events (like rename).
 */
MessageView.prototype.KIND_EVENTS = 6;
/**
 * Message with operators avatar
 *
 * This kind of message leaved only for compatibility with core
 */
MessageView.prototype.KIND_AVATAR = 7;

/** End of message kind constants */

/**
 * Register 'allowTags' Handlebars helper.
 *
 * This helper unescape HTML entities for allowed (span and strong) tags.
 */
Handlebars.registerHelper('allowTags', function(text) {
    var result = text;
    result = result.replace(
        /&lt;(span|strong)&gt;(.*?)&lt;\/\1&gt;/g,
        '<$1>$2</$1>'
    );
    result = result.replace(
        /&lt;span class=&quot;(.*?)&quot;&gt;(.*?)&lt;\/span&gt;/g,
        '<span class="$1">$2</span>'
    );
    return new Handlebars.SafeString(result);
});