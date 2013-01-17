/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

(function(Mibew, Handlebars, _) {

    /**
     * @class Represents Refresh control View
     */
    Mibew.Views.RefreshControl = Mibew.Views.Control.extend(
        /** @lends Mibew.Views.RefreshControl.prototype */
        {
            /**
             * Template function
             * @type Function
             */
            template: Handlebars.templates.refresh_control,

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
                    'click': 'refresh'
                }
            ),

            /**
             * Refresh chat window via model's refresh method
             */
            refresh: function() {
                this.model.refresh();
            }
        }
    );

})(Mibew, Handlebars, _);