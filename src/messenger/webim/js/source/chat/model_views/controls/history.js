/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

(function(Mibew, Handlebars, _) {

    /**
     * @class Represents History control view
     */
    Mibew.Views.HistoryControl = Mibew.Views.Control.extend(
        /** @lends Mibew.Views.HistoryControl.prototype */
        {
            /**
             * Template function
             * @type Function
             */
            template: Handlebars.templates.history_control,

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
                    'click': 'showHistory'
                }
            ),

            /**
             * Dispalay history window
             */
            showHistory: function() {
                var user = Mibew.Objects.Models.user;
                var link = this.model.get('link');
                if (user.get('isAgent') && link) {
                    var winParams = this.model.get('windowParams');

                    // TODO: Kill &amp; at the server side
                    link = link.replace('&amp;', '&', 'g');

                    var newWindow = window.open(link, 'UserHistory', winParams);
                    if (newWindow !== null) {
                        newWindow.focus();
                        newWindow.opener=window;
                    }
                }
            }
        }
    );

})(Mibew, Handlebars, _);