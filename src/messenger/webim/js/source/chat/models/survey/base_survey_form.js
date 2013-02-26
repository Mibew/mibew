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
                 * Contains following keys:
                 *  - 'select': string, HTML code for select box;
                 *  - 'descriptions': array, groups descriptions;
                 *  - 'defaultDescription': string, description for group that
                 *    selected by default.
                 *
                 * If there is no available groups this field is equal to null.
                 * @type Object
                 * @todo Create HTML code at client side
                 */
                groups: null
            }
        }
    );

})(Mibew);