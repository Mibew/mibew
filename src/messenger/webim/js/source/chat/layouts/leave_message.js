/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

(function(Mibew, Backbone){

    /**
     * Represents leave message page layout
     */
    Mibew.Layouts.LeaveMessage = Backbone.Marionette.Layout.extend(
        /** @lends Mibew.Layouts.LeaveMessage.prototype */
        {
            /**
             * Template function
             * @type Function
             */
            template: Handlebars.templates.leave_message_layout,

            /**
             * Regions list
             * @type Object
             */
            regions: {
                leaveMessageFormRegion: '#content-wrapper',
                descriptionRegion: '#description-region'
            },

            /**
             * Override Backbone.Marionette.ItemView.serializeData to pass some
             * extra fields to template.
             *
             * Use undocumented feature of layouts: passing data to template via
             * serializeData method.
             *
             * @returns {Object} Template data
             */
            serializeData: function() {
                return {
                    page: Mibew.Objects.Models.page.toJSON()
                }
            }
        }
    );

})(Mibew, Backbone);