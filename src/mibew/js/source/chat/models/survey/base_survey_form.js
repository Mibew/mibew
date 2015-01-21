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

(function(Mibew){

    /**
     * @class Represents base class for survey form model
     */
    Mibew.Models.BaseSurveyForm = Mibew.Models.Base.extend(
        /** @lends Mibew.Models.BaseSurveyForm.prototype */
        {
            /**
             * A list of default model values.
             * @type Object
             */
            defaults : {
                /**
                 * User name
                 * @type String
                 */
                name: '',

                /**
                 * User email
                 * @type String
                 */
                email: '',

                /**
                 * User message
                 * @type String
                 */
                message: '',

                /**
                 * User info
                 * @type String
                 */
                info: '',

                /**
                 * Page that user came from
                 * @type String
                 */
                referrer: '',

                /**
                 * Selected group id
                 * @type Number
                 */
                groupId: null,

                /**
                 * Available groups list.
                 *
                 * Contains objects with following keys:
                 *  - 'id': int, group id;
                 *  - 'name': string, group name;
                 *  - 'description': string, group description;
                 *  - 'online': boolean, indicates if group online;
                 *  - 'selected': boolean, indicates if group selected
                 *    by default.
                 *
                 * If there is no available groups this field is equal to null.
                 * @type Array
                 * @todo Create HTML code at client side
                 */
                groups: null
            }
        }
    );

})(Mibew);