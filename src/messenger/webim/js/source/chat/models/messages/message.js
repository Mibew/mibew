/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

(function(Mibew){

    /**
     * @class Base class for message models
     */
    Mibew.Models.Message = Mibew.Models.Base.extend(
        /** @lends Mibew.Models.Message.prototype */
        {
            /**
             * Default values of model
             */
            defaults : {
                /**
                 * Text of the message
                 * @type String
                 */
                message: ''
            }
        }
    );

})(Mibew);