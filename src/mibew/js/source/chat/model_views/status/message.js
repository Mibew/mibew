/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
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
            template: Handlebars.templates.chat_status_message
        }
    );

})(Mibew, Handlebars);