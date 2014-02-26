/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */

(function(Mibew, _){

    /**
     * @class Represents typing status model
     */
    Mibew.Models.StatusTyping = Mibew.Models.Status.extend(
        /** @lends Mibew.Models.StatusTyping.prototype */
        {
            /**
             * A list of default model values.
             *
             * The model inherits defaults from
             * {@link Mibew.Models.Status.prototype.defaults}.
             * @type Object
             */
            defaults: _.extend(
                {},
                Mibew.Models.Status.prototype.defaults,
                {
                    /**
                     * Indicates if status element is visible or not.
                     * @type Boolean
                     */
                    visible: false,

                    /**
                     * Time in milliseconds after that status message will be
                     * cleaned.
                     * @type Number
                     */
                    hideTimeout: 2000
                }
            ),

            /**
             * Returns model type
             * @returns {String} Model type
             */
            getModelType: function() {
                return 'StatusTyping'
            },

            /**
             * Make the status element visible
             * @todo May be move it to the view!?
             */
            show: function() {
                // Make typing status visible
                this.set({visible: true});

                // Hide it after a while
                this.autoHide();
            }
        }
    );

})(Mibew, _);