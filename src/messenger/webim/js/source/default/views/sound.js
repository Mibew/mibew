/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

(function(Mibew, Backbone, Handlebars){

    /**
     * @class Represents sound notification view
     */
    Mibew.Views.Sound = Backbone.Marionette.ItemView.extend(
        /** @lends Mibew.Views.Sound.prototype */
        {
            /**
             * Template function
             * @type Function
             */
            template: Handlebars.templates.sound,

            /**
             * Class name for view's DOM element
             * @type String
             */
            className: 'sound-player',

            /**
             * Map model events to the view methods
             * @type Object
             */
            modelEvents: {
                'sound:play': 'render'
            }
        }
    );

})(Mibew, Backbone, Handlebars);