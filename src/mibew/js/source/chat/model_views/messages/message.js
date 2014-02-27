/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
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