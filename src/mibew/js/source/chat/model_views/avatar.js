/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */

(function(Mibew, Backbone, Handlebars) {

    /**
     * @class Represents user avatar view
     */
    Mibew.Views.Avatar = Backbone.Marionette.ItemView.extend(
        /** @lends Mibew.Views.Avatar.prototype */
        {
            /**
             * Template function
             * @type Function
             */
            template: Handlebars.templates.chat_avatar,

            /**
             * Class name for view's DOM element
             * @type String
             */
            className: 'avatar',

            /**
             * Map model events to the view methods
             * @type Object
             */
            modelEvents: {
                'change': 'render'
            }
        }
    );

})(Mibew, Backbone, Handlebars);