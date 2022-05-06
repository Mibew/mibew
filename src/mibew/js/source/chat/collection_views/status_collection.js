/*!
 * This file is a part of Mibew Messenger.
 *
 * Copyright 2005-2022 the original author or authors.
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

(function(Mibew) {

    /**
     * @class Represents Status bar view
     */
    Mibew.Views.StatusCollection = Mibew.Views.CollectionBase.extend(
        /** @lends Mibew.Views.StatusCollection.prototype */
        {
            /**
             * Class name for view's DOM element
             * @type String
             */
            className: 'status-collection',

            /**
             * Returns default child view constructor.
             *
             * The function is used instead of "childView" property to provide
             * an ability to override child view constructor without this class
             * overriding.
             *
             * @param {Backbone.Model} model The model the view created for.
             * @returns {Backbone.Marionette.ItemView}
             */
            getChildView: function(model) {
                return Mibew.Views.Status;
            }
        }
    );

})(Mibew);