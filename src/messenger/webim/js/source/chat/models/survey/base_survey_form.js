/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
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