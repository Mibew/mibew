/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */

(function(Mibew, _){

    /**
     * @namespace Holds localization functions
     */
    Mibew.Localization = {};

    /**
     * Contains localization strings
     * @type Object
     */
    var localStrings = {};

    /**
     * Localize string
     * @param {String} str String for localization
     * @returns {String} Localized string
     */
    Mibew.Localization.get = function(str) {
        if (! localStrings.hasOwnProperty(str)) {
            return false;
        }
        return localStrings[str];
    }

    /**
     * Store localization object. Can be call multiple times, localization
     * objects will be merged.
     * @param {Object} strs Localization object
     */
    Mibew.Localization.set = function(strs) {
        _.extend(localStrings, strs);
    }

})(Mibew, _);