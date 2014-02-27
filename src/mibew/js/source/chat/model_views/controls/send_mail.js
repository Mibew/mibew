/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */

(function(Mibew, Handlebars, _) {

    /**
     * @class Represents Send mail control View
     */
    Mibew.Views.SendMailControl = Mibew.Views.Control.extend(
        /** @lends Mibew.Views.SendMailControl.prototype */
        {
            /**
             * Template function
             * @type Function
             */
            template: Handlebars.templates.chat_controls_send_mail,

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
                    'click': 'sendMail'
                }
            ),

            /**
             * Load and display send mail window
             */
            sendMail: function() {
                var link = this.model.get('link');
                var page = Mibew.Objects.Models.page;
                if (link) {
                    var winParams = this.model.get('windowParams');

                    var style = page.get('style');

                    // TODO: Kill &amp; at the server side
                    link = link.replace(/\&amp\;/g, '&')
                        + (style ? ('&style=' + style) : '');

                    var newWindow = window.open(link, 'ForwardMail', winParams);
                    if (newWindow !== null) {
                        newWindow.focus();
                        newWindow.opener=window;
                    }
                }
            }
        }
    );

})(Mibew, Handlebars, _);