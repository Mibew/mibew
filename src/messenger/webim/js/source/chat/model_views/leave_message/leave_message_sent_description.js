/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

(function(Mibew, Backbone, Handlebars){

    /**
     * @class Represents leave message sent description
     */
    Mibew.Views.LeaveMessageSentDescription = Backbone.Marionette.ItemView.extend(
        /** @lends Mibew.Views.LeaveMessageSentDescription.prototype */
        {
            /**
             * Template function
             * @type Function
             */
            template: Handlebars.templates.leave_message_sent_description,

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

})(Mibew, Backbone, Handlebars);