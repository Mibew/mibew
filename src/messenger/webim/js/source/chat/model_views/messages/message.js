/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

(function(Mibew, Handlebars) {

    /**
     * @class Represents message view
     */
    Mibew.Views.Message = Mibew.Views.Message.extend(
        /** @lends Mibew.Views.Message.prototype */
        {
            /**
             * Template function
             * @type Function
             */
            template: Handlebars.templates.chat_message
        }
    );

})(Mibew, Handlebars);