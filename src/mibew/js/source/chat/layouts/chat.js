/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */

(function(Mibew, Backbone){

    /**
     * Represents chat layout
     */
    Mibew.Layouts.Chat = Backbone.Marionette.Layout.extend(
        /** @lends Mibew.Layouts.Chat.prototype */
        {
            /**
             * Template function
             * @type Function
             */
            template: Handlebars.templates.chat_layout,

            /**
             * Regions list
             * @type Object
             */
            regions: {
                controlsRegion: '#controls-region',
                avatarRegion: '#avatar-region',
                messagesRegion: {
                    selector: '#messages-region',
                    regionType: Mibew.Regions.Messages
                },
                statusRegion: '#status-region',
                messageFormRegion: '#message-form-region'
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
                var models = Mibew.Objects.Models;
                return {
                    page: models.page.toJSON(),
                    user: models.user.toJSON()
                }
            }
        }
    );

})(Mibew, Backbone);