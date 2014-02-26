/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
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