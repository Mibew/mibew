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
     * @class Represents survey form view
     */
    Mibew.Views.SurveyForm = BaseView.extend(
        /** @lends Mibew.Views.SurveyForm.prototype */
        {
            /**
             * Template function
             * @type Function
             */
            template: Handlebars.templates['survey/form'],

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