/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 *
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

(function(Handlebars){
    /**
     * Register 'formatTimeToNow' Handlebars helper.
     *
     * This helper takes unix timestamp as argument and return difference
     * between current timestamp and passed one in "HH:MM:SS" format.
     */
    Handlebars.registerHelper('formatTimeSince', function(unixTimestamp){
        // Get time diff
        var diff = Math.round((new Date()).getTime() / 1000) - unixTimestamp;
        // Get time parts
        var seconds = diff % 60;
        var minutes = Math.floor(diff / 60) % 60;
        var hours = Math.floor(diff / (60 * 60));
        // Get result parts
        var result = [];
        if (hours > 0) {
            result.push(hours);
        }
        result.push(minutes < 10 ? '0' + minutes : minutes);
        result.push(seconds < 10 ? '0' + seconds : seconds);
        // Build result string
        return result.join(':');
    });
})(Handlebars);