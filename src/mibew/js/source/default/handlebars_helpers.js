/*!
 * This file is a part of Mibew Messenger.
 *
 * Copyright 2005-2014 the original author or authors.
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
     * Register 'apply' Handlebars helper.
     *
     * This helper provide an ability to apply several helpers to single
     * Handlebars expression
     *
     * Example of helper usage:
     * <code>
     * {{apply text "emHelper, strongHelper"}}
     * </code>
     * In the example above helpers will apply to text one after another: first
     * 'emHelper' and second 'strongHelper'.
     */
    Handlebars.registerHelper('apply', function(text, helpers) {
        var result = text;
        var validHelperName = /^[0-9A-z_]+$/;
        helpers = helpers.split(/\s*,\s*/);
        // Apply helpers one after another
        for (var prop in helpers) {
            if (! helpers.hasOwnProperty(prop) ||
                ! validHelperName.test(helpers[prop])) {
                continue;
            }
            if (typeof Handlebars.helpers[helpers[prop]] != 'function') {
                throw new Error(
                    "Unregistered helper '" + helpers[prop] + "'!"
                );
            }
            result = Handlebars.helpers[helpers[prop]](result).toString();
        }
        return new Handlebars.SafeString(result);
    });

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
     * Register 'nl2br' Handlebars helper.
     *
     * This helper replace all new line characters (\n) by 'br' tags
     */
    Handlebars.registerHelper('nl2br', function(text) {
        return new Handlebars.SafeString(
            text.toString().replace(/\n/g, "<br/>")
        );
    });

    /**
     * Register 'l10n' Handlebars helper
     *
     * This helper returns translated string with specified key
     */
    Handlebars.registerHelper('l10n', function(key) {
        return (Mibew.Localization.trans(key) || '');
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
        // Convert serch value to string and escape special regexp characters
        var searchPattern = search.toString().replace(
                /[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g,
                "\\$&"
            ),
            re = new RegExp(searchPattern, 'g');

        return options.fn(this).replace(re, replacement);
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
})(Mibew, Handlebars);