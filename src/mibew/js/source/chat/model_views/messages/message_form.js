/*!
 * This file is a part of Mibew Messenger.
 *
 * Copyright 2005-2015 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

(function(Mibew, Backbone, Handlebars) {

    /**
     * @class Represents Message Processor View
     */
    Mibew.Views.MessageForm = Backbone.Marionette.ItemView.extend(
        /** @lends Mibew.Views.MessageForm.prototype */
        {
            /**
             * Template function
             * @type Function
             */
            template: Handlebars.templates['chat/message_form'],

            /**
             * UI events hash.
             * Map UI events on the view methods.
             * @type Object
             */
            events: {
                'click #send-message': 'postMessage',
                'keydown #message-input': 'messageKeyDown',
                'keyup #message-input': 'checkUserTyping',
                'change #message-input': 'checkUserTyping',
                'change #predefined': 'selectPredefinedAnswer'
            },

            /**
             * Map model events to the view methods
             * @type Object
             */
            modelEvents: {
                'change': 'render'
            },

            /**
             * Shortcuts for ui elements
             * @type Object
             */
            ui: {
                message: '#message-input',
                send: '#send-message',
                predefinedAnswer: '#predefined'
            },

            /**
             * View initializer.
             */
            initialize: function() {
                Mibew.Objects.Models.user.on('change:canPost', this.render, this);
            },

            /**
             * Override Backbone.Marionette.ItemView.serializeData to pass some
             * extra fields to template.
             * @returns {Object} Template data
             */
            serializeData: function() {
                var data = this.model.toJSON();
                data.user = Mibew.Objects.Models.user.toJSON();
                data.sendShortcut = this.getSendShortcut();
                return data;
            },

            /**
             * Get, check and post message
             */
            postMessage: function() {
                var msg = this.ui.message.val();
                // TODO: Think about it
                // Cut multiple line breaks
                //msg = msg.replace(/(\r\n|\n|\r)+$/,"\n");
                if (msg != '') {
                    this.disableInput();
                    this.model.postMessage(msg);
                    Mibew.Objects.Collections.messages.once(
                        'multiple:add',
                        this.postMessageComplete,
                        this
                    );
                }
            },

            /**
             * Handler of key down event on the message input. Send message if
             * Enter/Ctrl+Enter pressed.
             */
            messageKeyDown: function(e) {
                var key = e.which;
                var ctrl = e.ctrlKey;
                // Keycode of Enter key is '10' for Mac and '13' for other
                // systems.
                // There is no traditional Ctrl key on Mac.
                if ((key == 13 && (ctrl || this.model.get('ignoreCtrl'))) || key == 10) {
                    this.postMessage();
                }
            },

            /**
             * Enable message input area
             */
            enableInput: function() {
                this.ui.message.removeAttr('disabled');
            },

            /**
             * Disable message input area
             */
            disableInput: function() {
                this.ui.message.attr('disabled', 'disabled');
            },

            /**
             * Clear message input area
             */
            clearInput: function() {
                this.ui.message
                    // Set empty value
                    .val('')
                    // And manually trigger jQuery change event on the message
                    // text area
                    .change();
            },

            /**
             * Callback function for message post.
             * Clear input area and enable it.
             */
            postMessageComplete: function() {
                this.clearInput();
                this.enableInput();
                // Always set focus on message input after message sent
                this.ui.message.focus();
            },

            /**
             * Set message to selected predefined answer and reset predefined
             * answer selectbox.
             */
            selectPredefinedAnswer: function() {
                var message = this.ui.message;
                var answer = this.ui.predefinedAnswer;
                var index = answer.get(0).selectedIndex;
                // Index should be set and not equals to zero
                if (index) {
                    // Set message
                    message
                        // Set new value
                        .val(this.model.get('predefinedAnswers')[index-1].full)
                        // And manually trigger jQuery change event on the
                        // message text area
                        .change();
                    message.focus();
                    // Reset predefined answer selector
                    answer.get(0).selectedIndex = 0;
                }
            },

            /**
             * Check if user typing and update user info
             */
            checkUserTyping: function() {
                var user = Mibew.Objects.Models.user;
                var isTyping = (this.ui.message.val() != '');
                if (isTyping != user.get('typing')) {
                    user.set({typing: isTyping});
                }
            },

            /**
             * Builds a string with send shortcut.
             *
             * @returns {String}
             */
            getSendShortcut: function() {
                if (this.model.get('ignoreCtrl')) {
                    return 'Enter';
                }

                // There is no Ctrl key on mac, thus we need to use a command
                // key instead.
                return (navigator.userAgent.indexOf('mac') !== -1)
                    ? '&#8984;-Enter'
                    : 'Ctrl-Enter';
            }
        }
    );

})(Mibew, Backbone, Handlebars);