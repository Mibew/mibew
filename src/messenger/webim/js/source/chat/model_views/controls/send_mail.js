/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
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
            template: Handlebars.templates.send_mail_control,

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