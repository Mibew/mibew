/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */

(function(Mibew, Handlebars, _) {

    /**
     * @class Represents cound control view
     */
    Mibew.Views.SoundControl = Mibew.Views.Control.extend(
        /** @lends Mibew.Views.SoundControl.prototype */
        {
            /**
             * Template function
             * @type Function
             */
            template: Handlebars.templates.chat_controls_sound,

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
                    'click': 'toggle'
                }
            ),

            /**
             * Toggle sound state
             */
            toggle: function() {
                this.model.toggle();
            }
        }
    );

})(Mibew, Handlebars, _);