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

(function(Mibew, Backbone){

    /**
     * Represents leave message page layout
     */
    Mibew.Layouts.LeaveMessage = Backbone.Marionette.LayoutView.extend(
        /** @lends Mibew.Layouts.LeaveMessage.prototype */
        {
            /**
             * Template function
             * @type Function
             */
            template: Handlebars.templates['leave_message/layout'],

            /**
             * Regions list
             * @type Object
             */
            regions: {
                leaveMessageFormRegion: '#content-wrapper',
                descriptionRegion: '#description-region'
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
                return {
                    page: Mibew.Objects.Models.page.toJSON()
                }
            }
        }
    );

})(Mibew, Backbone);