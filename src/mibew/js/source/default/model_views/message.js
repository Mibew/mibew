/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
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
                msg.allowFormatting = (messageKind != this.model.KIND_USER
                    && messageKind != this.model.KIND_AGENT);
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
                if (kind == this.model.KIND_USER) {
                    return "user";
                }
                if (kind == this.model.KIND_AGENT) {
                    return "agent";
                }
                if (kind == this.model.KIND_FOR_AGENT) {
                    return "hidden";
                }
                if (kind == this.model.KIND_INFO) {
                    return "inf";
                }
                if (kind == this.model.KIND_CONN) {
                    return "conn";
                }
                if (kind == this.model.KIND_EVENTS) {
                    return "event";
                }
                if (kind == this.model.KIND_PLUGIN) {
                    return "plugin";
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
            }
        }
    );

})(Mibew, Backbone, Handlebars);