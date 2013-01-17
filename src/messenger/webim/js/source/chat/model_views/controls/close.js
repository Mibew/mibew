/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

(function(Mibew, Handlebars, _) {

    /**
     * @class Represents Close control view
     */
    Mibew.Views.CloseControl = Mibew.Views.Control.extend(
        /** @lends Mibew.Views.CloseControl.prototype */
        {
            /**
             * Template function
             * @type Function
             */
            template: Handlebars.templates.close_control,

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
                    'click': 'close'
                }
            ),

            /**
             * Display confirmation dialog and close chat window
             */
            close: function() {
                // Show confirmation message if can
                var confirmMessage = Mibew.Localization.get('chat.close.confirmation');
                if (confirmMessage !== false) {
                    if (! confirm(confirmMessage)) {
                        return;
                    }
                }
                this.model.closeThread();
            }
        }
    );

})(Mibew, Handlebars, _);