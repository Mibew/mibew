/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
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