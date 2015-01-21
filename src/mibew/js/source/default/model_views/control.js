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
     * @class Represents default control. Implement some basic functionality.
     */
    Mibew.Views.Control = Backbone.Marionette.ItemView.extend(
        /** @lends Mibew.Views.Control.prototype */
        {
            /**
             * Template function
             * @type Function
             */
            template: Handlebars.templates['default_control'],

            /**
             * Map model events to the view methods
             * @type Object
             */
            modelEvents: {
                'change': 'render'
            },
            /**
             * Map ui events to view methods. Use as default for all child
             * views.
             * @type Object
             */
            events: {
                'mouseover': 'mouseOver',
                'mouseleave': 'mouseLeave'
            },

            /**
             * Generate hash of view's DOM element attributes. Add default CSS
             * classes whose names based on the result of model's 'getModelType'
             * method.
             */
            attributes: function() {
                // Init classes list
                var classes = [];

                // Add default for all controls CSS class
                classes.push('control');

                // Add CSS class from className properti of the view
                if (this.className) {
                    classes.push(this.className);
                    // Prevent using className property instead of result of
                    // this method
                    this.className = '';
                }

                // Add CSS class based on model type
                var controlType = this.getDashedControlType();
                if (controlType) {
                    classes.push(controlType);
                }
                return {
                    'class': classes.join(' ')
                }
            },

            /**
             * Handles mouse over event on the control. Add 'active' CSS class
             * to the view's DOM element.
             */
            mouseOver: function() {
                var controlType = this.getDashedControlType();
                this.$el.addClass(
                    'active' +
                    (controlType ? '-' + controlType : '' )
                );
            },

            /**
             * Handles mouse leave event on the control. Remove 'active' CSS
             * class from the view's DOM element.
             */
            mouseLeave: function() {
                var controlType = this.getDashedControlType();
                this.$el.removeClass(
                    'active' +
                    (controlType ? '-' + controlType : '' )
                );
            },

            /**
             * Create dasherized version of the model type or use cached one.
             * @returns {Strring} Model type
             */
            getDashedControlType: function() {
                if (typeof this.dashedControlType == 'undefined') {
                    // There is no control type in the cache
                    this.dashedControlType = Mibew.Utils.toDashFormat(
                        this.model.getModelType()
                    ) || '';
                }
                return this.dashedControlType;
            }
        }
    );

})(Mibew, Backbone, Handlebars);