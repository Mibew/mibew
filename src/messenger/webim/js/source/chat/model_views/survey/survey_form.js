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
     * @class Represents survey form view
     */
    Mibew.Views.SurveyForm = BaseView.extend(
        /** @lends Mibew.Views.SurveyForm.prototype */
        {
            /**
             * Template function
             * @type Function
             */
            template: Handlebars.templates.survey_form,

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
                    'click #submit-survey': 'submitForm'
                }
            ),

            /**
             * Check form field, update model field and call model.submit()
             * method.
             */
            submitForm: function() {
                this.showAjaxLoader();

                var values = {};

                // Update group id
                if (this.model.get('groups')) {
                    values.groupId = this.ui.groupSelect.val()
                }

                // Update name
                if (this.model.get('canChangeName')) {
                    values.name = this.ui.name.val() || '';
                }

                // Update email
                if (this.model.get('showEmail')) {
                    values.email = this.ui.email.val() || '';
                }

                // Update message
                if (this.model.get('showMessage')) {
                    values.message = this.ui.message.val() || '';
                }

                // Update model fields
                this.model.set(values, {validate: true});

                // Submit form
                this.model.submit();
            }
        }
    );

})(Mibew, Handlebars, _);