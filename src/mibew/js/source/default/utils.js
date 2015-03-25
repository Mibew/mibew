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

(function(Mibew, $, _, vex, validator){

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

        var parts = str.match(/((?:[A-Z]?[a-z0-9]+)|(?:[A-Z][a-z0-9]*))/g);
        if (!parts) {
            // It seems that the sting has no convertible parts.
            return '';
        }

        for(var i = 0; i < parts.length; i++) {
            parts[i] = parts[i].toLowerCase();
        }

        return parts.join('-');
    }

    /**
     * Check if email address valid or not.
     *
     * The method play nice with addresses that have national characters in the
     * domain part.
     *
     * @param {String} email Address to check
     * @returns {Boolean} true if address is valid and false otherwise
     */
    Mibew.Utils.checkEmail = function(email) {
        // The problem is "validator.isEmail" cannot be used because it's not
        // fully compatible with RFC 2822. See
        // {@link https://github.com/chriso/validator.js/issues/377} for
        // details. Thus we need a custom validation method for emails.
        if (!email) {
            return false;
        }

        var chunks = email.split('@');

        if (chunks.length < 2) {
            // There is no "@" character in the address thus it cannot be valid.
            return false;
        }

        var domain = chunks.pop(),
            localPart = chunks.join('@');

        if (!validator.isFQDN(domain)) {
            // The domain part is invalid.
            return false;
        }

        // The regular exprassion is base on RFC 2822. It's not fully compatible
        // with RFC but is sutabe for most real cases.
        return /^(([a-zA-Z0-9!#$%&'*+\-/=?\^_`{|}~]+(\.[a-zA-Z0-9!#$%&'*+\-/=?\^_`{|}~]+)*)|(\".+\"))$/.test(localPart);
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

    /**
     * Builds params string for window.open method.
     * @param {Object} options List of options to use in the target string.
     * @returns {String}
     */
    Mibew.Utils.buildWindowParams = function(options) {
        var allOptions = _.defaults({}, options, {
            toolbar: false,
            scrollbars: false,
            location: false,
            status: true,
            menubar: false,
            width: 640,
            heght: 480,
            resizable: true
        });

        return [
            'toolbar=' + (allOptions.toolbar ? '1' : '0'),
            'scrollbars=' + (allOptions.scrollbars ? '1' : '0'),
            'location=' + (allOptions.location ? '1' : '0'),
            'status=' + (allOptions.status ? '1' : '0'),
            'menubar=' + (allOptions.menubar ? '1' : '0'),
            'width=' + allOptions.width,
            'height=' + allOptions.height,
            'resizable=' + (allOptions.resizable ? '1' : '0')
        ].join(',');
    }

    /**
     * Sets default options for Vex dialogs.
     * @type {Function}
     */
    var setVexDefaults = _.once(function() {
        if (!vex.defaultOptions.className) {
            vex.defaultOptions.className = 'vex-theme-default';
        }
        vex.dialog.buttons.YES.text = Mibew.Localization.trans('OK');
        vex.dialog.buttons.NO.text = Mibew.Localization.trans('Cancel');
    });

    /**
     * Checks if vex dialog is already opened.
     * @returns {Boolean}
     */
    var isVexOpened = function () {
        return (vex.getAllVexes().length > 0);
    }

    /**
     * Alerts a message.
     * @param {String} message A message that should be displayed.
     */
    Mibew.Utils.alert = function(message) {
        setVexDefaults();
        if (isVexOpened()) {
            // Do not open alert if one already opened.
            return;
        }
        vex.dialog.alert({message: message});
    }

    /**
     * Requires user confirmation.
     * @param {String} message An assertion that should be confirmed.
     * @param {Function} callback A function that will be called when the
     * dialog is closed. This function accepts only one argument which is
     * boolean that represents confirmation result.
     */
    Mibew.Utils.confirm = function(message, callback) {
        setVexDefaults();
        vex.dialog.confirm({
            message: message,
            callback: callback
        });
    }

    /**
     * Requests some info from the user.
     * @param {String} message A message that will be displayed to user.
     * @param {Function} callback A function that will be called when the
     * dialog is closed. This function accepts only one argument which is
     * the entered result.
     */
    Mibew.Utils.prompt = function(message, callback) {
        setVexDefaults();
        vex.dialog.prompt({
            message: message,
            callback: callback
        });
    }

})(Mibew, jQuery, _, vex, validator);