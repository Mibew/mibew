/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
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
            template: Handlebars.templates.chat_controls_close,

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
                    'click': 'closeThread'
                }
            ),

            /**
             * Display confirmation dialog and close chat window
             */
            closeThread: function() {
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