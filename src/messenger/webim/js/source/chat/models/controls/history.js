/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

(function(Mibew, _){

    /**
     * @class History control model
     */
    Mibew.Models.HistoryControl = Mibew.Models.Control.extend(
        /** @lends Mibew.Models.HistoryControl.prototype */
        {
            /**
             * A list of default model values.
             *
             * The model inherits defaults from
             * {@link Mibew.Models.Control.prototype.defaults}.
             * @type Object
             */
            defaults: _.extend(
                {},
                Mibew.Models.Control.prototype.defaults,
                {
                    /**
                     * An URL of the History page or false by default.
                     * @type String|Boolean
                     */
                    link: false,

                    /**
                     * Params string for history popup window
                     * @type String
                     */
                    windowParams: ''
                }
            ),

            /**
             * Returns model type
             * @returns {String} Model type
             */
            getModelType: function() {
                return 'HistoryControl';
            }

        }
    );

})(Mibew, _);