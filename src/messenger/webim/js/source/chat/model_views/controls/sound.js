/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
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
                this.model.set({
                    enabled: !this.model.get('enabled')
                });
            }
        }
    );

})(Mibew, Handlebars, _);