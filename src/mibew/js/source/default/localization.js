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
     * Localize string.
     *
     * @param {String} str String for localization.
     * @param {...String} placeholder A value that will replace a placeholder.
     * @returns {String} Localized string.
     */
    Mibew.Localization.trans = function(str) {
        // Replace "{n}" style placeholders with specified arguments. The first
        // argument is skipped because it is the localized string.
        var placeholders = Array.prototype.slice.call(arguments, 1);

        // If there is no localized string use passed in one.
        var localized = localStrings.hasOwnProperty(str) ? localStrings[str] : str;

        return localized.replace(/\{([0-9]+)\}/g, function(match, index) {
            return placeholders[parseInt(index)] || '';
        });
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