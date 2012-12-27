/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */


(function(Mibew){

    /**
     * @class Base class for controls
     */
    Mibew.Models.Control = Mibew.Models.Base.extend(
        /** @lends Mibew.Models.Control.prototype */
        {
            /**
             * A list of model default values.
             */
            defaults : {

                /**
                 * Control title
                 * @type String
                 */
                title: '',

                /**
                 * Control weight. Used for ordering controls.
                 * @type Number
                 */
                weight: 0
            }
        }
    );

})(Mibew);