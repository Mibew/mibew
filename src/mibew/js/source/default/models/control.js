/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */

(function(Mibew){

    /**
     * @class Base class for controls
     */
    Mibew.Models.Control = Mibew.Models.Base.extend(
        /** @lends Mibew.Models.Control.prototype */
        {
            /**
             * A list of model default values.
             */
            defaults : {

                /**
                 * Control title
                 * @type String
                 */
                title: '',

                /**
                 * Control weight. Used for ordering controls.
                 * @type Number
                 */
                weight: 0
            }
        }
    );

})(Mibew);