/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */

(function(Mibew, _){

    /**
     * @class Represents status message model
     */
    Mibew.Models.StatusMessage = Mibew.Models.Status.extend(
        /** @lends Mibew.Models.StatusMessage.prototype */
        {
            /**
             * A list of default model values.
             * The model inherits defaults from
             * {@link Mibew.Models.Status.prototype.defaults}.
             * @type Object
             */
            defaults: _.extend(
                {},
                Mibew.Models.Status.prototype.defaults,
                {
                    /**
                     * Text of the status message.
                     * @type String
                     */
                    message: '',

                    /**
                     * Indicates if status message is visible or not.
                     * @type Boolean
                     */
                    visible: false
                }
            ),

            /**
             * Returns model type
             * @returns {String} Model type
             */
            getModelType: function() {
                return 'StatusMessage'
            },

            /**
             * Update status message
             * @param {String} msg New status message
             */
            setMessage: function(msg) {
                // Update status message
                this.set({
                    message: msg,
                    visible: true
                });

                // Hide status message after a while
                this.autoHide();
            }
        }
    );

})(Mibew, _);