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

(function(Mibew, Backbone, _){

    /**
     * Return special contructor for an item view if it exists or the
     * default constructor otherwise. Use in Mibew.Views.CollectionBase and
     * Mibew.Views.CompositeBase
     * @private
     * @param {Backbone.Model} item Collection item
     * @param {Function} ChildViewType Default item view constructor
     * @param {Object} childViewOptions Additional item view options
     * @returns Item view instance
     */
    var buildChildView = function(item, ChildViewType, childViewOptions) {
        // Build options object
        var options = _.extend({model: item}, childViewOptions);
        // Try to find special view for this model
        if (typeof item.getModelType != 'function') {
            return new ChildViewType(options);
        }
        var modelType = item.getModelType();
        if (modelType && Mibew.Views[modelType]) {
            return new Mibew.Views[modelType](options);
        } else {
            return new ChildViewType(options);
        }
    }

    /**
     * @class Represents base collection view
     */
    Mibew.Views.CollectionBase = Backbone.Marionette.CollectionView.extend(
        /** @lends Mibew.Views.CollectionBase.prototype */
        {
            /**
             * Default item view constructor.
             * @type Function
             */
            childView: Backbone.Marionette.ItemView,

            /**
             * Return special contructor for an item view if it exists or the
             * default constructor otherwise.
             */
            buildChildView: buildChildView
        }
    );

    Mibew.Views.CompositeBase = Backbone.Marionette.CompositeView.extend(
        /** @lends Mibew.Views.CompositeBase.prototype */
        {
            /**
             * Return special contructor for an item view if it exists or the
             * default constructor otherwise.
             */
            buildChildView: buildChildView
        }
    );

})(Mibew, Backbone, _);