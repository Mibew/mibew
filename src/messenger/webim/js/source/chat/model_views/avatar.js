/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
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
            template: Handlebars.templates.avatar,

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