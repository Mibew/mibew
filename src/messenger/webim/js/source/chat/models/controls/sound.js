/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

(function(Mibew, _){

    /**
     * @class Represents toggle sound control model
     */
    Mibew.Models.SoundControl = Mibew.Models.Control.extend(
        /** @lends Mibew.Models.SoundControl.prototype */
        {
            /**
             * A list of default model values.
             * The model inherits defaults from
             * {@link Mibew.Models.Control.prototype.defaults}.
             * @type Object
             */
            defaults: _.extend(
                {},
                Mibew.Models.Control.prototype.defaults,
                {
                    /**
                     * Indicates if sound enable.
                     * @type Boolean
                     */
                    enabled: true
                }
            ),

            /**
             * Returns model type
             * @returns {String} Model type
             */
            getModelType: function() {
                return 'SoundControl'
            }
        }
    );

})(Mibew, _);