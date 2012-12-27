/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

(function(Mibew, Handlebars) {

    /**
     * @class Represents status message view
     */
    Mibew.Views.StatusMessage = Mibew.Views.Status.extend(
        /** @lends Mibew.Views.StatusMessage.prototype */
        {
            /**
             * Template function
             * @type Function
             */
            template: Handlebars.templates.status_message
        }
    );

})(Mibew, Handlebars);