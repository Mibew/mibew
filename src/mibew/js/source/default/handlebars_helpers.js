/*!
 * This file is a part of Mibew Messenger.
 *
 * Copyright 2005-2015 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

(function(Mibew, Handlebars){
    /**
     * Register 'formatTime' Handlebars helper.
     *
     * This helper takes unix timestamp as argument and return time in
     * "HH:MM:SS"
     * format
     */
    Handlebars.registerHelper('formatTime', function(unixTimestamp){
        var d = new Date(unixTimestamp * 1000);
        // Get time parts
        var hours = d.getHours().toString();
        var minutes = d.getMinutes().toString();
        var seconds = d.getSeconds().toString();
        // Add leading zero if needed
        hours = hours < 10 ? '0' + hours : hours;
        minutes = minutes < 10 ? '0' + minutes : minutes;
        seconds = seconds < 10 ? '0' + seconds : seconds;
        // Build result string
        return hours + ':' + minutes + ':' + seconds;
    });

    /**
     * Register 'urlReplace' Handlebars helper.
     *
     * This helper serch URLs and replace them by 'a' tag
     */
    Handlebars.registerHelper('urlReplace', function(text) {
        return new Handlebars.SafeString(
            text.toString().replace(
                /((?:https?|ftp):\/\/\S*)/g,
                '<a href="$1" target="_blank">$1</a>'
            )
        );
    });

    /**
     * Register 'l10n' Handlebars helper
     *
     * This helper returns translated string with specified key. Example of usage:
     * <code>
     *   {{l10n "localization.string" arg1 arg2 arg3}}
     * </code>
     * where:
     *   - "localization.string" is localization constant.
     *   - arg* are arguments that will replace the placeholders.
     */
    Handlebars.registerHelper('l10n', function() {
        var l = Mibew.Localization,
            slice = Array.prototype.slice;

        return l.trans.apply(l, slice.call(arguments));
    });

    /**
     * Register "ifEven" helper.
     *
     * This helper checks if specified value is even or not. Example of usage:
     * <code>
     *   {{#ifEven value}}
     *     The value is even.
     *   {{else}}
     *     The value is odd.
     *   {{/ifEven}}
     * </code>
     */
    Handlebars.registerHelper('ifEven', function(value, options) {
        if ((value % 2) === 0) {
            return options.fn(this);
        } else {
            return options.inverse(this);
        }
    });

    /**
     * Register "ifOdd" helper.
     *
     * This helper checks if specified value is odd or not. Example of usage:
     * <code>
     *   {{#ifOdd value}}
     *     The value is odd.
     *   {{else}}
     *     The value is even.
     *   {{/ifOdd}}
     * </code>
     */
    Handlebars.registerHelper('ifOdd', function(value, options) {
        if ((value % 2) !== 0) {
            return options.fn(this);
        } else {
            return options.inverse(this);
        }
    });

    /**
     * Registers "ifAny" helper.
     *
     * This helper checks if at least one argumet can be treated as
     * "true" value. Example of usage:
     * <code>
     *   {{#ifAny first second third}}
     *     At least one of argument can be threated as "true".
     *   {{else}}
     *     All values are "falsy"
     *   {{/ifAny}}
     * </code>
     */
    Handlebars.registerHelper('ifAny', function() {
        var argsCount = arguments.length,
            // The last helper's argument is the options hash. We need it to
            // render the template.
            options = arguments[argsCount - 1],
            // All other helper's arguments are values that are used to evalute
            // condition. Exctract that values from arguments pseudo array.
            values = [].slice.call(arguments, 0, argsCount - 1);

        for (var i = 0, l = values.length; i < l; i++) {
            if (values[i]) {
                // A true value is found. Render the positive block.
                return options.fn(this);
            }
        }

        // All values are "falsy". Render the negative block.
        return options.inverse(this);
    });

    /**
     * Registers "ifEqual" helper.
     *
     * This helper checks if two values are equal or not. Example of usage:
     * <code>
     *   {{#ifEqual first second}}
     *     The first argument is equal to the second one.
     *   {{else}}
     *     The arguments are not equal.
     *   {{/ifEqual}}
     * </code>
     */
    Handlebars.registerHelper('ifEqual', function(left, right, options) {
        // Not strict equality is used intentionally here.
        if (left == right) {
            return options.fn(this);
        } else {
            return options.inverse(this);
        }
    });

    /**
     * Registers "repeat" helper.
     *
     * This helper repeats a string specified number of times. Example of usage:
     * <code>
     *   {{#repeat times}}content to repeat{{/repeat}}
     * </code>
     */
    Handlebars.registerHelper('repeat', function(count, options) {
        var result = '',
            content = options.fn(this);

        for (var i = 0; i < count; i++) {
            result += content;
        }

        return result;
    });

    /**
     * Registers "replace" helper.
     *
     * This helper replaces all found substrings with the specifed replacement.
     * Example of usage:
     * <code>
     *   {{#replace search replacement}}target content{{/replace}}
     * </code>
     */
    Handlebars.registerHelper('replace', function(search, replacement, options) {
        var unescapedSearch = search
            // Allow using new line character
            .replace(/\\n/g, '\n')
            // Allow using tab character
            .replace(/\\t/g, '\t')
            // Allow using all UTF characters in \uXXX format.
            .replace(/\\u([A-Za-z0-9])/g, function(match, code) {
                return String.fromCharCode(parseInt(code, 16));
            });

        return options.fn(this).split(unescapedSearch).join(replacement);
    });

    /**
     * Registers "cutString" helper.
     *
     * This helper cuts a string if it exceeds specified length. Example of
     * usage:
     * <code>
     *   {{cutString string length}}
     * </code>
     */
    Handlebars.registerHelper('cutString', function(length, options) {
        return options.fn(this).substr(0, length);
    });

    /**
     * Registers "block" helper.
     *
     * This helper defines default content of a block. Example of usage:
     * <code>
     *   {{#block "blockName"}}
     *     Default content for the block
     *   {{/block}}
     * </code>
     */
    Handlebars.registerHelper('block', function(name, options) {
        if (this._blocksStorage && this._blocksStorage.hasOwnProperty(name)) {
            return this._blocksStorage[name];
        }

        return options.fn(this);
    });

    /**
     * Registers "extends" helper.
     *
     * This is used for templates inheritance. Example of usage:
     * <code>
     *   {{#extends "parentTemplateName"}}
     *     {{#override "blockName"}}
     *       Overridden first block
     *     {{/override}}
     *
     *     {{#override "anotherBlockName"}}
     *       Overridden second block
     *     {{/override}}
     *   {{/extends}}
     * </code>
     */
    Handlebars.registerHelper('extends', function(parentTemplate, options) {
        // Create a blocks storage. If the current inheritance level is not the
        // deepest one, a storage already exists. In this case we do not need
        // to override it.
        this._blocksStorage = this._blocksStorage || {};

        // Render content inside "extends" helper to override blocks
        options.fn(this);

        // Check if the parent template exists
        if (!Handlebars.templates.hasOwnProperty(parentTemplate)) {
            throw Error('Parent template "' + parentTemplate + '" is not defined');
        }

        // Render the parent template. We assume that templates are stored in
        // Handlebars.templates property. It is the most common case and take
        // place when templates were compiled with node.js Handlebars CLI tool.
        return Handlebars.templates[parentTemplate](this);
    });

    /**
     * Registers "override" helper.
     *
     * This helper overrides content of a block. Example of usage:
     * <code>
     *   {{#extends "parentTemplateName"}}
     *     {{#override "blockName"}}
     *       Overridden first block
     *     {{/override}}
     *
     *     {{#override "anotherBlockName"}}
     *       Overridden second block
     *     {{/override}}
     *   {{/extends}}
     * </code>
     */
    Handlebars.registerHelper('override', function(name, options) {
        // We need to provide unlimited inheritence level. Rendering is started
        // from the deepest level template. If the content is in the block
        // storage it is related with the deepest level template. Thus we do not
        // need to override it.
        if (!this._blocksStorage.hasOwnProperty(name)) {
            this._blocksStorage[name] = options.fn(this);
        }

        // An empty string is returned for consistency.
        return '';
    });

    /**
     * Registers "ifOverridden" helper.
     *
     * This helper checks if a block is overridden or not. Example of usage:
     * <code>
     *   {{#ifOverridden "blockName"}}
     *     The block was overridden
     *   {{else}}
     *     The block was not overridden
     *   {{/ifOverridden}}
     * </code>
     */
    Handlebars.registerHelper('ifOverridden', function(name, options) {
        if (this._blocksStorage && this._blocksStorage.hasOwnProperty(name)) {
            return options.fn(this);
        } else {
            return options.inverse(this);
        }
    });

    /**
     * Registers "unlessOverridden" helper.
     *
     * This helper checks if a block is overridden or not. Example of usage:
     * <code>
     *   {{#unlessOverridden "blockName"}}
     *     The block was not overridden
     *   {{else}}
     *     The block was overridden
     *   {{/unlessOverridden}}
     * </code>
     */
    Handlebars.registerHelper('unlessOverridden', function(name, options) {
        if (this._blocksStorage && this._blocksStorage.hasOwnProperty(name)) {
            return options.inverse(this);
        } else {
            return options.fn(this);
        }
    });
})(Mibew, Handlebars);