/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 *
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
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
     * Register 't' Handlebars helper
     *
     * This helper returns translated string with specified key
     */
    Handlebars.registerHelper('L10n', function(key) {
        return (Mibew.Localization.get(key) || '');
    });
})(Mibew, Handlebars);