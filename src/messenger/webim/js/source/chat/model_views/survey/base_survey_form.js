/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
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
                errors: '.errors'
            },

            /**
             * Map model events to the view methods
             * @type Object
             */
            modelEvents: {
                'invalid': 'showError',
                'submit:error': 'showError'
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
                var groupId = this.ui.groupSelect.prop('selectedIndex');
                var descriptions = this.model.get('groups').descriptions || [];
                this.ui.groupDescription.text(descriptions[groupId] || '');
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
            }
        }
    );

})(Mibew, Backbone);