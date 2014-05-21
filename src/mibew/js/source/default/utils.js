/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */

(function(Mibew, $){

    /**
     * @namespace Holds utility functions
     */
    Mibew.Utils = {};

    /**
     * Cast first character of a string to upper case
     * @param {String} str Input string
     * @returns {String} Result string
     */
    Mibew.Utils.toUpperCaseFirst = function(str) {
        if (typeof str != 'string') {
            return false;
        }
        if (str === '') {
            return str;
        }
        return str.substring(0, 1).toUpperCase() + str.substring(1);
    }

    /**
     * Cast string in camel case to dash format.
     * For example, if input string is 'anInputString' the result string will be
     * 'an-input-string'
     * @param {String} str Input string
     * @returns {String} Result string
     */
    Mibew.Utils.toDashFormat = function(str) {
        if (typeof str != 'string') {
            return false;
        }
        var parts = str.match(/((?:[A-Z]?[a-z]+)|(?:[A-Z][a-z]*))/g);
        for(var i = 0; i < parts.length; i++) {
            parts[i] = parts[i].toLowerCase();
        }
        return parts.join('-');
    }

    /**
     * Check if email address valid or not
     * @param {String} email Address to check
     * @returns {Boolean} true if address is valid and false otherwise
     */
    Mibew.Utils.checkEmail = function(email) {
        return /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(email);
    }

    /**
     * Play .wav or .mp3 sound file
     * @param {String} file File path (without extension)
     */
    Mibew.Utils.playSound = function (file) {

        var player = $('audio[data-file="'+file+'"]');
        if (player.length > 0) {
            player.get(0).play();
        } else {
            var audioTag = $("<audio>", {autoplay: true, style: "display: none"}).append(
            '<source src="' + file + '.wav" type="audio/x-wav" />' +
            '<source src="' + file + '.mp3" type="audio/mpeg" codecs="mp3" />' +
            '<embed src="' + file + '.wav" type="audio/x-wav" hidden="true" autostart="true" loop="false" />'
            );
            $('body').append(audioTag);
            if ($.isFunction(audioTag.get(0).play)) {
                audioTag.attr('data-file', file);
            }
        }
    }

})(Mibew, $);