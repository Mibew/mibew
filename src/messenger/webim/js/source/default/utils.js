/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

(function(Mibew){

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

})(Mibew);