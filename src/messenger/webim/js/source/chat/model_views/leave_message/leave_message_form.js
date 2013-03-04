/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
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
            template: Handlebars.templates.leave_message_form,

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
                    'submit:error': 'showError submitError'
                }
            ),

            /**
             * Update model fields and call model.submit() method.
             */
            submitForm: function() {
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