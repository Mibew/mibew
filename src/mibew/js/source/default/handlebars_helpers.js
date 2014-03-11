/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
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
            text.replace(
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
        return new Handlebars.SafeString(text.replace(/\n/g, "<br/>"));
    });

    /**
     * Register 'l10n' Handlebars helper
     *
     * This helper returns translated string with specified key
     */
    Handlebars.registerHelper('l10n', function(key) {
        return (Mibew.Localization.get(key) || '');
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
})(Mibew, Handlebars);