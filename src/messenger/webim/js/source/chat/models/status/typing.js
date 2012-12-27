/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
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