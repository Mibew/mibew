/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
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