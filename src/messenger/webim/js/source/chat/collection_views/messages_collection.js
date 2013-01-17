/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

(function(Mibew) {

    /**
     * @class Represents messages list view
     */
    Mibew.Views.MessagesCollection = Mibew.Views.CollectionBase.extend(
        /** @lends Mibew.Views.MessagesCollection.prototype */
        {
            /**
             * Default item view constructor.
             * @type Function
             */
            itemView: Mibew.Views.Message,

            /**
             * Class name for view's DOM element
             * @type String
             */
            className: 'messages-collection',

            /**
             * View initializer.
             */
            initialize: function() {
                this.collection.on('multiple:add', this.messagesAdded, this);
                Mibew.Objects.Models.messageForm.on(
                    'before:post',
                    this.messagePost,
                    this
                );
            },

            /**
            * Indicates if next sound should be skipped
            * @type Boolean
            */
            skipNextSound: true,

            /**
             * Messages form 'after:post' event handler. Disable next sound.
             */
            messagePost: function() {
                this.skipNextSound = true;
            },

            /**
            * Messages collection 'add:all' event handler. Try to play sound
            * about new message
            */
            messagesAdded: function() {
                if (! this.skipNextSound) {
                    // Check if sound disabled by user via sound control
                    if (Mibew.Objects.Models.Controls.sound.get('enabled')) {
                        // Build sound path
                        var path = Mibew.Objects.Models.page.get('webimRoot');
                        if (path) {
                            path += '/sounds/new_message.wav';
                            // Play sound
                            Mibew.Objects.Models.sound.play(path);
                        }
                    }
                }
                this.skipNextSound = false;
            }
        }
    );

})(Mibew);