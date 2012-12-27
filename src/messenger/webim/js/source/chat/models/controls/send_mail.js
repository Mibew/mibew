/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

(function(Mibew, _){

    /**
     * @class Represent Send mail control model
     */
    Mibew.Models.SendMailControl = Mibew.Models.Control.extend(
        /** @lends Mibew.Models.SendMailControl.prototype */
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
                     * An URL of the Mail page or false by default.
                     * @type String|Boolean
                     */
                    link: false
                }
            ),
            /**
             * Returns model type
             * @returns {String} Model type
             */
            getModelType: function() {
                return 'SendMailControl';
            }
        }
    );

})(Mibew, _);