/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */

(function(Mibew, Backbone){

    /**
     * @class Represents base class for survey form view
     */
    Mibew.Views.BaseSurveyForm = Backbone.Marionette.ItemView.extend(
        /** @lends Mibew.Views.BaseSurveyForm.prototype */
        {
            /**
             * Map ui events to view methods.
             * @type Object
             */
            events: {
                'change select[name="group"]': 'changeGroupDescription',
                'submit form': 'preventSubmit'
            },

            /**
             * Shortcuts for ui elements
             * @type Object
             */
            ui: {
                groupSelect: 'select[name="group"]',
                groupDescription: '#groupDescription',
                name: 'input[name="name"]',
                email: 'input[name="email"]',
                message: 'textarea[name="message"]',
                errors: '.errors',
                ajaxLoader: '#ajax-loader'
            },

            /**
             * Map model events to the view methods
             * @type Object
             */
            modelEvents: {
                'invalid': 'hideAjaxLoader showError',
                'submit:error': 'hideAjaxLoader showError'
            },

            /**
             * Prevent form submitting
             */
            preventSubmit: function(event) {
                event.preventDefault();
            },

            /**
             * Change group description
             */
            changeGroupDescription: function() {
                var selectedIndex = this.ui.groupSelect.prop('selectedIndex');
                var description = this.model
                    .get('groups')[selectedIndex]
                    .description || '';
                this.ui.groupDescription.text(description);
            },

            /**
             * Display error messages
             * @param Array errors Array of errors
             */
            showError: function(model, error) {
                var errorMessage;
                if (typeof error == 'string') {
                    errorMessage = error;
                } else {
                    errorMessage = error.message;
                }
                // TODO: Think about moving this to template
                this.ui.errors.html(errorMessage);
            },

            /**
             * Override Backbone.Marionette.ItemView.serializeData to pass some
             * extra fields to template.
             *
             * Pass page data to template.
             *
             * @returns {Object} Template data
             */
            serializeData: function() {
                var data = this.model.toJSON();
                data.page = Mibew.Objects.Models.page.toJSON();
                return data;
            },

            /**
             * Shows ajax loader
             */
            showAjaxLoader: function() {
                this.ui.ajaxLoader.show();
            },

            /**
             * Hide ajax loader
             */
            hideAjaxLoader: function() {
                this.ui.ajaxLoader.hide();
            }
        }
    );

})(Mibew, Backbone);