/*!
 * This file is a part of Mibew Messenger.
 *
 * Copyright 2005-2014 the original author or authors.
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
     * @class Represents controls bar
     */
    Mibew.Views.ControlsCollection = Mibew.Views.CollectionBase.extend(
        /** @lends Mibew.Views.ControlsCollection.prototype */
        {
            /**
             * Default item view constructor.
             * @type Function
             */
            childView: Mibew.Views.Control,

            /**
             * Class name for view's DOM element
             * @type String
             */
            className: 'controls-collection'
        }
    );

})(Mibew);