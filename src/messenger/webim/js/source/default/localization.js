/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
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