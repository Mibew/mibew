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

(function(Mibew, Handlebars, _){

    // Create shortcut for base view
    var BaseView = Mibew.Views.BaseSurveyForm;

    /**
     * @class Represents leave message form view
     */
    Mibew.Views.LeaveMessageForm = BaseView.extend(
        /** @lends Mibew.Views.LeaveMessageForm.prototype */
        {
            /**
             * Template function
             * @type Function
             */
            template: Handlebars.templates['leave_message/form'],

            /**
             * Map ui events to view methods.
             * The view inherits events from
             * {@link Mibew.Views.BaseSurveyForm.prototype.events}.
             * @type Object
             */
            events: _.extend(
                {},
                BaseView.prototype.events,
                {
                    'click #send-message': 'submitForm'
                }
            ),

            /**
             * Shortcuts for ui elements.
             * The view inherits ui elements from
             * {@link Mibew.Views.BaseSurveyForm.prototype.ui}.
             * @type Object
             */
            ui: _.extend(
                {},
                BaseView.prototype.ui,
                {
                    captcha: 'input[name="captcha"]',
                    captchaImg: '#captcha-img'
                }
            ),

            modelEvents: _.extend(
                {},
                BaseView.prototype.modelEvents,
                {
                    'submit:error': 'hideAjaxLoader showError submitError'
                }
            ),

            /**
             * Update model fields and call model.submit() method.
             */
            submitForm: function() {
                this.showAjaxLoader();

                // Update model fields
                var values = {};

                // Update group id
                if (this.model.get('groups')) {
                    values.groupId = this.ui.groupSelect.val()
                }

                // Update name
                values.name = this.ui.name.val() || '';

                // Update email
                values.email = this.ui.email.val() || '';

                // Update message
                values.message = this.ui.message.val() || '';

                if (this.model.get('showCaptcha')) {
                    values.captcha = this.ui.captcha.val() || '';
                }

                // Update model fields
                this.model.set(values, {validate: true});

                // Submit form
                this.model.submit();
            },

            /**
             * Handler function for model 'submitError' event.
             * Update captcha img if captcha field has wrong value.
             *
             * @param {Mibew.Models.LeaveMessageForm} model Form model
             * @param {Object} error Error object, contains 'code' and 'message'
             * fields
             */
            submitError: function(model, error) {
                if (error.code == model.ERROR_WRONG_CAPTCHA && model.get('showCaptcha')) {
                    var src = this.ui.captchaImg.attr('src');
                    src = src.replace(/\?d\=[0-9]+/, '');
                    this.ui.captchaImg.attr(
                        'src',
                        src + '?d=' + (new Date()).getTime()
                    );
                }
            }
        }
    );

})(Mibew, Handlebars, _);