/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */

(function(Mibew, _){

    /**
     * @class Represents base status model
     */
    Mibew.Models.Status = Mibew.Models.Base.extend(
        /** @lends Mibew.Models.Status */
        {
            /**
             * A list of default model values.
             * @type Object
             */
            defaults: {
                /**
                 * Indicates if status element is visible or not.
                 * @type Boolean
                 */
                visible: true,

                /**
                 * Use for ordering status elements in status collection.
                 * @type Number
                 */
                weight: 0,

                /**
                 * Time in milliseconds after that status message will be
                 * cleaned.
                 * @type Number
                 */
                hideTimeout: 4000,

                /**
                 * Title of status element.
                 * @type String
                 */
                title: ''
            },

            /**
             * Class initializer
             */
            initialize: function () {
                /**
                 * Timer for hiding status element
                 */
                this.hideTimer = null;
            },

            /**
            * Automatically hide status element after timeout
            * @param {Number} [timeout] Timeout in milliseconds before hide
            * status element. Default value is this.get('hideTimeout').
            */
            autoHide: function(timeout) {
                var delay = timeout || this.get('hideTimeout');

                // Clear timer if it runs
                if (this.hideTimer) {
                    clearTimeout(this.hideTimer);
                }

                // Hide status element after a while
                this.hideTimer = setTimeout(
                    _.bind(
                        function(){this.set({visible: false});},
                        this
                    ),
                    delay
                );
            }
        }
    );

})(Mibew, _);