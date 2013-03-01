/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

(function(Mibew, Handlebars, _) {

    /**
     * @class Represents Secure mode control View
     */
    Mibew.Views.SecureModeControl = Mibew.Views.Control.extend(
        /** @lends Mibew.Views.SecureModeControl.prototype */
        {
            /**
             * Template function
             * @type Function
             */
            template: Handlebars.templates.chat_controls_secure_mode,

            /**
             * Map ui events to view methods
             * The view inherits events from
             * {@link Mibew.Views.Control.prototype.events}.
             * @type Object
             */
            events: _.extend(
                {},
                Mibew.Views.Control.prototype.events,
                {
                    'click': 'secure'
                }
            ),

            /**
             * Move to secure chat
             */
            secure: function() {
                var link = this.model.get('link')
                if (link) {
                    var style = Mibew.Objects.Models.page.get('style');
                    window.location.href = link.replace(/\&amp\;/g, '&')
                        + (style ? ('&style=' + style) : '');
                }
            }
        }
    );

})(Mibew, Handlebars, _);