/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */

(function(Mibew, Backbone, Handlebars){

    /**
     * @class Represents leave message description
     */
    Mibew.Views.LeaveMessageDescription = Backbone.Marionette.ItemView.extend(
        /** @lends Mibew.Views.LeaveMessageDescription.prototype */
        {
            /**
             * Template function
             * @type Function
             */
            template: Handlebars.templates.leave_message_description,

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