/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

(function(Mibew, Handlebars) {

    /**
     * @class Represents typing status
     */
    Mibew.Views.StatusTyping = Mibew.Views.Status.extend(
        /** @lends Mibew.Views.StatusTyping.protoype */
        {
            /**
             * Template function
             * @type Function
             */
            template: Handlebars.templates.chat_status_typing
        }
    );

})(Mibew, Handlebars);