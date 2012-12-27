/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

(function(Mibew, Backbone, Handlebars) {

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
     * @class Represents default message view
     */
    Mibew.Views.Message = Backbone.Marionette.ItemView.extend(
        /** @lends Mibew.Views.Message.prototype */
        {
            /**
             * Template function
             * @type Function
             */
            template: Handlebars.templates.message,

            /**
             * Class name for view's DOM element
             * @type String
             */
            className: 'message',

            /**
             * Map model events to the view methods
             * @type Object
             */
            modelEvents: {
                'change': 'render'
            },

            /**
             * Override Backbone.Marionette.ItemView.serializeData to pass some
             * extra fields to template.
             * @returns {Object} Template data
             */
            serializeData: function() {
                var msg = this.model.toJSON();
                var messageKind = this.model.get('kind');

                // Add message fields
                msg.allowFormatting = (messageKind != this.KIND_USER
                    && messageKind != this.KIND_AGENT);
                msg.kindName = this.kindToString(messageKind);
                msg.message = this.escapeString(msg.message);

                return msg;
            },

            /**
             * Map message kide code to kind name
             * @param {Number} kind Kind code
             * @returns {String} Kind name
             */
            kindToString: function(kind) {
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
            },

            /**
             * Replace HTML special characters('<', '>', '&', "'", '"', '`') by
             * corresponding HTML entities.
             *
             * @param {String} str Unescaped string
             * @returns {String} Escaped string
             */
            escapeString: function(str) {
                return str.replace(
                    badCharRegEx,
                    function(chr) {
                        return badCharList[chr] || "&amp;";
                    }
                );
            },

            /** Message kind constants */

            /** Message sent by user. */
            KIND_USER: 1,

            /** Message sent by operator */
            KIND_AGENT: 2,

            /** Hidden system message to operator */
            KIND_FOR_AGENT: 3,

            /** System messages for user and operator */
            KIND_INFO: 4,

            /** Message for user if operator have connection problems */
            KIND_CONN: 5,

            /** System message about some events (like rename). */
            KIND_EVENTS: 6,

            /**
             * Message with operators avatar
             *
             * This kind of message leaved only for compatibility with core
             */
            KIND_AVATAR: 7

            /** End of message kind constants */
        }
    );

})(Mibew, Backbone, Handlebars);