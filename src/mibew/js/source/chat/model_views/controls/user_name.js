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

(function(Mibew, Handlebars, _) {

    Mibew.Views.UserNameControl = Mibew.Views.Control.extend(
        /** @lends Mibew.Views.UserNameControl.prototype */
        {
            /**
             * Template function
             * @type Function
             */
            template: Handlebars.templates['chat/controls/user_name'],

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
                    'click .user-name-control-set': 'changeName',
                    'click .user-name-control-change': 'showNameInput',
                    'keydown #user-name-control-input': 'inputKeyDown'
                }
            ),

            /**
             * Define shortcuts for ui elements
             * @type Object
             */
            ui: {
                'nameInput': '#user-name-control-input'
            },

            /**
             * View initializer
             */
            initialize: function() {
                // Hide name input on every user name change
                Mibew.Objects.Models.user.on(
                    'change:name',
                    this.hideNameInput,
                    this
                );

                // Show or hide name input by default
                this.nameInput = Mibew.Objects.Models.user.get('defaultName');
            },

            /**
             * Override Backbone.Marionette.ItemView.serializeData to pass some
             * extra fields to template.
             * @returns {Object} Template data
             */
            serializeData: function() {
                var data = this.model.toJSON();
                data.user = Mibew.Objects.Models.user.toJSON();
                data.nameInput = this.nameInput;
                return data;
            },

            /**
             * Handles key down event on the name input
             * @param {Event} e Event object
             */
            inputKeyDown: function(e) {
                var key = e.which;
                if (key == 13 || key == 10) {
                    // Change name after Enter key pressed
                    this.changeName();
                }
            },

            /**
             * Hide name input and rerender the view
             */
            hideNameInput: function() {
                this.nameInput = false;
                this.render();
            },

            /**
             * Show name input and rerender the view
             */
            showNameInput: function() {
                this.nameInput = true;
                this.render();
            },

            /**
             * Change user name
             */
            changeName: function () {
                var newName = this.ui.nameInput.val();
                this.model.changeName(newName);
            }
        }
    );

})(Mibew, Handlebars, _);